# Laravel Cashier Integration Specification

## Overview
This specification covers the integration of Laravel Cashier with Stripe for subscription management, trial periods, and billing operations in the Antipoll platform.

## Cashier Setup

### Installation and Configuration
```bash
# Install Laravel Cashier
composer require laravel/cashier-stripe

# Publish Cashier configuration
php artisan vendor:publish --tag="cashier-migrations"

# Run Cashier migrations
php artisan migrate

# Publish Cashier config
php artisan vendor:publish --tag="cashier-config"
```

### Environment Configuration
```env
# .env file
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxxxxx
CASHIER_CURRENCY=usd
CASHIER_LOGGER=stripe
```

### Cashier Configuration
```php
// config/cashier.php
return [
    'model' => App\Models\Organization::class,
    'currency' => 'usd',
    'locale' => 'en',
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => 200,
        'middleware' => 'throttle:60,1',
    ],
    'trial' => [
        'days' => 14,
    ],
];
```

## Organization Model with Cashier

### Billable Trait Integration
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Organization extends Model
{
    use Billable;

    protected $fillable = [
        'name', 'user_id', 'personal',
        'stripe_id', 'trial_ends_at', 'pm_type',
        'card_brand', 'card_last_four',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'personal' => 'boolean',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    // Cashier methods
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->active();
    }

    // Custom methods for trial management
    public function startTrial()
    {
        $this->update([
            'trial_ends_at' => now()->addDays(config('cashier.trial.days')),
        ]);

        $this->owner->notify(new TrialStartedNotification());
    }

    public function getTrialDaysRemainingAttribute()
    {
        if (!$this->onTrial()) {
            return 0;
        }

        return $this->trial_ends_at->diffInDays(now());
    }

    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasExpiredTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function subscribed($planName)
    {
        return $this->subscription && 
               $this->subscription->stripe_plan === $planName && 
               $this->subscription->active();
    }

    public function getPlanLimitAttribute()
    {
        if (!$this->subscription) {
            return config('subscriptions.plans.starter.features.active_polls');
        }

        return config("subscriptions.plans.{$this->subscription->stripe_plan}.features.active_polls");
    }
}
```

## Subscription Service with Cashier

### Subscription Management Service
```php
<?php

namespace App\Services;

use App\Models\Organization;
use Laravel\Cashier\Cashier;

class SubscriptionService
{
    public function createSubscription(Organization $organization, string $plan)
    {
        // Create or retrieve Stripe customer
        if (!$organization->stripe_id) {
            $organization->createAsStripeCustomer();
        }

        // Create subscription with 14-day trial using Cashier
        $subscription = $organization->newSubscription($plan)
            ->trialDays(config('cashier.trial.days'))
            ->create();

        return $subscription;
    }

    public function createTrialCheckout(Organization $organization, string $plan)
    {
        // Create Stripe Checkout Session using Cashier
        return $organization->newSubscription($plan)
            ->trialDays(config('cashier.trial.days'))
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url' => route('billing.plans'),
                'payment_method_types' => ['card', 'apple_pay', 'google_pay'],
                'allow_promotion_codes' => true,
                'customer_update' => [
                    'address' => 'auto',
                    'name' => 'auto',
                ],
                'tax_id_collection' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'organization_id' => $organization->id,
                    'plan_type' => $plan,
                ],
            ]);
    }

    public function upgradeSubscription(Organization $organization, string $newPlan)
    {
        $subscription = $organization->subscription();
        
        if (!$subscription) {
            return $this->createSubscription($organization, $newPlan);
        }

        // Swap to new plan with proration using Cashier
        return $subscription->swap($newPlan);
    }

    public function downgradeSubscription(Organization $organization, string $newPlan)
    {
        $subscription = $organization->subscription();
        
        if (!$subscription) {
            throw new Exception('No active subscription found');
        }

        // Swap to lower plan with proration
        return $subscription->swap($newPlan);
    }

    public function cancelSubscription(Organization $organization, $immediate = false)
    {
        $subscription = $organization->subscription();
        
        if (!$subscription) {
            return false;
        }

        if ($immediate) {
            return $subscription->cancelNow();
        }

        return $subscription->cancel();
    }

    public function cancelSubscriptionAtPeriodEnd(Organization $organization)
    {
        $subscription = $organization->subscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->cancelAtPeriodEnd();
    }

    public function resumeSubscription(Organization $organization)
    {
        $subscription = $organization->subscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->resume();
    }
}
```

## Cashier Webhook Handling

### Webhook Controller
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * Handle webhook events from Stripe
     */
    public function handleWebhook(Request $request)
    {
        // Cashier handles webhook verification automatically
        return parent::handleWebhook($request);
    }

    /**
     * Handle customer subscription created
     */
    public function handleCustomerSubscriptionCreated($payload)
    {
        $organization = Organization::where('stripe_id', $payload->data->object->customer)->first();
        
        if ($organization) {
            // Cashier automatically creates subscription record
            // Add custom logic here if needed
            $organization->owner->notify(new SubscriptionStartedNotification());
        }
    }

    /**
     * Handle invoice payment succeeded
     */
    public function handleInvoicePaymentSucceeded($payload)
    {
        $subscriptionId = $payload->data->object->subscription;
        $subscription = \App\Models\Subscription::where('stripe_id', $subscriptionId)->first();
        
        if ($subscription) {
            // Update subscription status
            $subscription->update([
                'stripe_status' => 'active',
                'last_payment_at' => now(),
            ]);

            // Send payment confirmation
            $subscription->organization->owner->notify(new PaymentSuccessfulNotification());
        }
    }

    /**
     * Handle customer subscription deleted
     */
    public function handleCustomerSubscriptionDeleted($payload)
    {
        $subscriptionId = $payload->data->object->id;
        $subscription = \App\Models\Subscription::where('stripe_id', $subscriptionId)->first();
        
        if ($subscription) {
            // Cashier automatically handles subscription cancellation
            $subscription->organization->owner->notify(new SubscriptionCanceledNotification());
        }
    }

    /**
     * Handle invoice payment failed
     */
    public function handleInvoicePaymentFailed($payload)
    {
        $subscriptionId = $payload->data->object->subscription;
        $subscription = \App\Models\Subscription::where('stripe_id', $subscriptionId)->first();
        
        if ($subscription) {
            // Handle payment failure
            $subscription->update([
                'stripe_status' => 'past_due',
                'last_payment_failed_at' => now(),
            ]);

            // Send payment failure notification
            $subscription->organization->owner->notify(new PaymentFailedNotification());
        }
    }
}
```

## Livewire Components with Cashier

### Billing Dashboard Component
```php
<?php

use App\Models\Organization;
use Livewire\Component;

new class extends Component {
    public Organization $organization;
    public $subscription;
    public $usageMetrics;
    public $trialDaysRemaining;
    public $upcomingInvoice;
    
    public function mount()
    {
        $this->organization = auth()->user()->currentOrganization;
        $this->refreshData();
    }
    
    public function upgradePlan($planId)
    {
        $subscriptionService = new \App\Services\SubscriptionService();
        $checkoutUrl = $subscriptionService->createTrialCheckout($this->organization, $planId);
        
        return redirect($checkoutUrl);
    }
    
    public function cancelSubscription()
    {
        $subscriptionService = new \App\Services\SubscriptionService();
        $subscriptionService->cancelSubscription($this->organization);
        $this->refreshData();
    }
    
    public function updatePaymentMethod()
    {
        // Redirect to Cashier payment method update
        return redirect($this->organization->updatePaymentMethod());
    }
    
    public function downloadInvoice($invoiceId)
    {
        // Use Cashier for invoice download
        return $this->organization->downloadInvoice($invoiceId);
    }
    
    public function refreshData()
    {
        $this->subscription = $this->organization->activeSubscription();
        $this->usageMetrics = $this->organization->getCurrentUsageMetrics();
        $this->upcomingInvoice = $this->organization->upcomingInvoice();
        $this->trialDaysRemaining = $this->organization->trial_days_remaining ?? 0;
    }
};
?>
<!-- Billing dashboard with Cashier integration -->
<div class="space-y-6">
    <!-- Trial Status -->
    @if($user->onTrial())
        <flux:callout variant="info">
            <flux:callout.content>
                <strong>{{ $trialDaysRemaining }} days left in your free trial!</strong>
                Choose a plan below to continue using Antipoll after your trial ends.
            </flux:callout.content>
        </flux:callout>
    @endif

    <!-- Current Subscription -->
    @if($subscription)
        <flux:grid>
            <flux:col>
                <flux:card>
                    <flux:card.header>Current Plan</flux:card.header>
                    <flux:card.content>
                        <div class="text-3xl font-bold">{{ $subscription->name }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $subscription->stripe_status === 'trialing' ? $trialDaysRemaining . ' days left in trial' : 'Billed $' . $subscription->quantity . ' × $' . number_format($subscription->amount / 100, 2) . '/' . $subscription->stripe_plan }}
                        </div>
                    </flux:card.content>
                </flux:card>
            </flux:col>
            
            <flux:col>
                <flux:card>
                    <flux:card.header>Usage</flux:card.header>
                    <flux:card.content>
                        <div class="text-3xl font-bold">{{ $usageMetrics->active_polls_count }} / {{ $organization->plan_limit }}</div>
                        <div class="text-sm text-gray-600">
                            Active polls this month
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($usageMetrics->active_polls_count / $organization->plan_limit) * 100 }}%"></div>
                        </div>
                    </flux:card.content>
                </flux:card>
            </flux:col>
        </flux:grid>
    @endif

    <!-- Quick Actions -->
    <flux:card>
        <flux:card.header>Quick Actions</flux:card.header>
        <flux:card.content>
            <flux:grid class="grid-cols-1 md:grid-cols-3 gap-4">
                <flux:col>
                    <flux:button wire:click="upgradePlan('professional')" variant="outline">
                        Upgrade Plan
                    </flux:button>
                </flux:col>
                
                <flux:col>
                    <flux:button wire:click="updatePaymentMethod()" variant="outline">
                        Update Payment Method
                    </flux:button>
                </flux:col>
                
                <flux:col>
                    @if($upcomingInvoice)
                        <flux:button wire:click="downloadInvoice({{ $upcomingInvoice->id }})" variant="outline">
                            Download Invoice
                        </flux:button>
                    @endif
                </flux:col>
            </flux:grid>
        </flux:card.content>
    </flux:card>
</div>
```

## Database Schema with Cashier

### Cashier Tables
```sql
-- Cashier creates these tables automatically
-- subscriptions: Main subscription records
-- subscription_items: Subscription line items
-- receipts: Payment receipts

-- Additional tables for Antipoll
CREATE TABLE usage_metrics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    metric_date DATE NOT NULL,
    active_polls_count INT DEFAULT 0,
    responses_count INT DEFAULT 0,
    plan_limit INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_organization_date (organization_id, metric_date),
    INDEX idx_metric_date (metric_date)
);
```

### Organizations Table Extensions
```sql
-- Add Cashier columns to organizations table
ALTER TABLE organizations ADD COLUMN stripe_id VARCHAR(255) NULL AFTER name;
ALTER TABLE organizations ADD COLUMN trial_ends_at TIMESTAMP NULL AFTER stripe_id;
ALTER TABLE organizations ADD COLUMN pm_type VARCHAR(255) NULL AFTER trial_ends_at;
ALTER TABLE organizations ADD COLUMN card_brand VARCHAR(255) NULL AFTER pm_type;
ALTER TABLE organizations ADD COLUMN card_last_four VARCHAR(4) NULL AFTER card_brand;

-- Add indexes for Cashier columns
ALTER TABLE organizations ADD INDEX idx_stripe_id (stripe_id);
ALTER TABLE organizations ADD INDEX idx_trial_ends_at (trial_ends_at);
```

## Routes with Cashier

### Webhook Routes
```php
// routes/web.php
use App\Http\Controllers\StripeWebhookController;

// Cashier webhook routes
Route::get('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

// Cashier payment method routes
Route::get('/payment-methods/update', function () {
    return auth()->user()->currentOrganization->updatePaymentMethod();
})->middleware('auth')->name('payment-methods.update');

Route::get('/invoices/{invoiceId}/download', function ($invoiceId) {
    return auth()->user()->currentOrganization->downloadInvoice($invoiceId);
})->middleware('auth')->name('invoices.download');
```

## Testing with Cashier

### Test Environment Setup
```php
// config/cashier.php (testing environment)
return [
    'model' => App\Models\Organization::class,
    'currency' => 'usd',
    'key' => env('STRIPE_KEY'), // pk_test_...
    'secret' => env('STRIPE_SECRET'), // sk_test_...
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => 200,
        'middleware' => 'throttle:60,1',
    ],
    'environment' => 'test',
];
```

### Test Scenarios
```php
// Test subscription creation
class SubscriptionTest extends TestCase
{
    public function test_trial_subscription_creation()
    {
        $organization = Organization::factory()->create();
        
        $subscription = $organization->newSubscription('starter')
            ->trialDays(14)
            ->create();
            
        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('trialing', $subscription->stripe_status);
        $this->assertNotNull($subscription->trial_ends_at);
    }

    public function test_subscription_upgrade()
    {
        $organization = Organization::factory()->create();
        $subscription = $organization->newSubscription('starter')->create();
        
        $upgradedSubscription = $subscription->swap('professional');
        
        $this->assertEquals('professional', $upgradedSubscription->stripe_plan);
    }

    public function test_subscription_cancellation()
    {
        $organization = Organization::factory()->create();
        $subscription = $organization->newSubscription('starter')->create();
        
        $canceledSubscription = $subscription->cancelNow();
        
        $this->assertEquals('canceled', $canceledSubscription->stripe_status);
    }
}
```

## Security Best Practices

### Cashier Security
- **Environment Variables**: Never commit Stripe keys to version control
- **Webhook Security**: Always verify webhook signatures
- **PCI Compliance**: Let Cashier handle PCI compliance
- **Data Validation**: Validate all Stripe data before processing

### Payment Security
- **Secure Checkout**: Use Cashier's hosted checkout pages
- **Tokenization**: Never store raw payment information
- **3D Secure**: Let Cashier handle SCA when required
- **Error Handling**: Graceful handling of payment failures

### Access Control
- **Subscription Authorization**: Ensure users can only manage their organization's subscriptions
- **Plan Limits**: Enforce plan limits at organization level
- **Audit Logging**: Log all subscription management actions
- **Rate Limiting**: Prevent abuse of subscription management

## Performance Optimization

### Caching Strategies
- **Subscription Cache**: Cache active organization subscription data
- **Usage Metrics Cache**: Cache organization usage calculations
- **Plan Configuration Cache**: Cache plan details
- **Webhook Processing**: Queue webhook handlers for performance

### Database Optimization
- **Indexes**: Proper indexes on subscription tables
- **Query Optimization**: Efficient subscription queries
- **Connection Pooling**: Manage database connections efficiently
- **Background Jobs**: Process heavy operations asynchronously

This specification provides a comprehensive foundation for implementing Laravel Cashier with Stripe for subscription management in the Antipoll platform.