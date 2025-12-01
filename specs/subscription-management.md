# Subscription Management System Specification

## Overview
This specification covers the SaaS subscription management system, 14-day free trial implementation, and Stripe integration for the Antipoll platform.

## Free Trial System

### Trial Configuration
- **Duration**: 14 days from signup
- **Feature Access**: Full access to all platform features
- **No Payment Required**: Start immediately without credit card
- **Trial Limits**: No limits on poll creation or responses during trial
- **Trial Tracking**: Real-time trial days remaining display

### Trial Conversion Flow
```php
// Trial management in User model
class User extends Authenticatable
{
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
}
```

### Trial Expiration Handling
```php
// Trial expiration job
class HandleTrialExpiration extends Command
{
    public function handle()
    {
        $expiredUsers = User::where('trial_ends_at', '<', now())
            ->whereNull('subscription_id')
            ->get();
            
        foreach ($expiredUsers as $user) {
            // Downgrade to free tier
            $user->update(['status' => 'expired_trial']);
            
            // Send expiration email
            $user->notify(new TrialExpiredNotification());
        }
    }
}
```

## Subscription Plans

### Plan Configuration
```php
// Plan configuration in config/subscriptions.php
return [
    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price_id' => 'price_starter_monthly',
            'amount' => 2900, // $29.00 in cents
            'currency' => 'usd',
            'interval' => 'month',
            'features' => [
                'active_polls' => 10,
                'responses' => 'unlimited',
                'analytics' => 'basic',
                'support' => 'email',
                'email_platforms' => 'all',
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'price_id' => 'price_professional_monthly',
            'amount' => 7900, // $79.00 in cents
            'currency' => 'usd',
            'interval' => 'month',
            'features' => [
                'active_polls' => 50,
                'responses' => 'unlimited',
                'analytics' => 'advanced',
                'support' => 'priority_email',
                'email_platforms' => 'all',
                'ab_testing' => true,
                'segmentation' => true,
            ],
        ],
        'business' => [
            'name' => 'Business',
            'price_id' => 'price_business_monthly',
            'amount' => 19900, // $199.00 in cents
            'currency' => 'usd',
            'interval' => 'month',
            'features' => [
                'active_polls' => 'unlimited',
                'responses' => 'unlimited',
                'analytics' => 'enterprise',
                'support' => 'dedicated_manager',
                'email_platforms' => 'all',
                'ab_testing' => true,
                'segmentation' => true,
                'white_label' => true,
                'custom_domains' => true,
                'team_seats' => 10,
            ],
        ],
    ],
];
```

### Subscription Model
```php
// Subscription model
class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'stripe_subscription_id', 'plan_type', 'status',
        'trial_ends_at', 'current_period_start', 'current_period_end',
        'canceled_at', 'ends_at', 'stripe_price_id', 'amount',
        'currency', 'interval', 'metadata'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function isActive()
    {
        return in_array($this->status, ['trialing', 'active']) && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function onTrial()
    {
        return $this->status === 'trialing';
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    public function getPlanNameAttribute()
    {
        return config("subscriptions.plans.{$this->plan_type}.name");
    }

    public function getPlanLimitAttribute()
    {
        return config("subscriptions.plans.{$this->plan_type}.features.active_polls");
    }
}
```

## Laravel Cashier Integration

### Cashier Subscription Setup
```php
// User model with Cashier trait
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;

    protected $fillable = [
        'name', 'email', 'password',
        'stripe_id', 'trial_ends_at', 'pm_type'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getTrialDaysRemainingAttribute()
    {
        if (!$this->onTrial()) {
            return 0;
        }

        return $this->trial_ends_at->diffInDays(now());
    }

    public function subscribed($planName)
    {
        return $this->subscription && 
               $this->subscription->stripe_plan === $planName && 
               $this->subscription->active();
    }
}
```

### Cashier Subscription Creation
```php
// Subscription service using Cashier
class SubscriptionService
{
    public function createSubscription(User $user, string $plan)
    {
        // Create or retrieve Stripe customer
        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        // Create subscription using Cashier
        $subscription = $user->newSubscription($plan)
            ->trialDays(14) // 14-day trial
            ->create();

        return $subscription;
    }

    public function createTrialCheckout(User $user, string $plan)
    {
        // Create Stripe Checkout Session using Cashier
        return $user->newSubscription($plan)
            ->trialDays(14)
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url' => route('billing.cancel'),
                'payment_method_types' => ['card', 'apple_pay', 'google_pay'],
                'allow_promotion_codes' => true,
                'customer_update' => [
                    'address' => 'auto',
                    'name' => 'auto',
                ],
                'tax_id_collection' => [
                    'enabled' => true,
                ],
            ]);
    }

    public function upgradeSubscription(User $user, string $newPlan)
    {
        $subscription = $user->subscription();
        
        if (!$subscription) {
            return $this->createSubscription($user, $newPlan);
        }

        // Swap to new plan with proration
        return $subscription->swap($newPlan);
    }

    public function cancelSubscription(User $user, $immediate = false)
    {
        $subscription = $user->subscription();
        
        if (!$subscription) {
            return false;
        }

        if ($immediate) {
            return $subscription->cancelNow();
        }

        return $subscription->cancel();
    }
}
```

### Plan Configuration with Cashier
```php
// config/cashier.php
return [
    'plan' => [
        'starter' => [
            'name' => 'Starter',
            'price' => 'price_starter_monthly',
            'features' => [
                'active_polls' => 10,
                'responses' => 'unlimited',
                'analytics' => 'basic',
                'support' => 'email',
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'price' => 'price_professional_monthly',
            'features' => [
                'active_polls' => 50,
                'responses' => 'unlimited',
                'analytics' => 'advanced',
                'support' => 'priority_email',
                'ab_testing' => true,
            ],
        ],
        'business' => [
            'name' => 'Business',
            'price' => 'price_business_monthly',
            'features' => [
                'active_polls' => 'unlimited',
                'responses' => 'unlimited',
                'analytics' => 'enterprise',
                'support' => 'dedicated_manager',
                'white_label' => true,
                'team_seats' => 10,
            ],
        ],
    ],
];
```

### Trial Management with Cashier
```php
// Trial management using Cashier
class TrialService
{
    public function startTrial(User $user)
    {
        // Set trial end date (14 days from now)
        $user->update([
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Send trial welcome email
        $user->notify(new TrialStartedNotification());
    }

    public function convertTrialToSubscription(User $user, string $plan)
    {
        if (!$user->onTrial()) {
            throw new Exception('User is not on trial');
        }

        // Create subscription that starts after trial
        $subscription = $user->newSubscription($plan)
            ->trialDays(0) // End trial immediately
            ->create();

        // Clear trial end date
        $user->update(['trial_ends_at' => null]);

        return $subscription;
    }

    public function checkTrialExpiration()
    {
        $expiringTrials = User::where('trial_ends_at', '<=', now()->addDays(3))
            ->where('trial_ends_at', '>', now())
            ->whereNull('stripe_id') // No active subscription
            ->get();

        foreach ($expiringTrials as $user) {
            $daysRemaining = $user->trial_days_remaining;
            
            if ($daysRemaining <= 3) {
                $user->notify(new TrialExpiringSoonNotification($daysRemaining));
            }
        }

        // Handle expired trials
        $expiredTrials = User::where('trial_ends_at', '<=', now())
            ->whereNull('stripe_id')
            ->get();

        foreach ($expiredTrials as $user) {
            $user->update(['trial_ends_at' => null]);
            $user->notify(new TrialExpiredNotification());
        }
    }
}
```

### Cashier Webhook Handling
```php
// Cashier webhook controller
class CashierWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Cashier handles webhook verification automatically
        $payload = $request->getContent();
        
        // Handle webhook using Cashier's webhook handler
        $type = $request->header('Stripe-Signature') 
            ? 'stripe' 
            : null;

        if ($type === 'stripe') {
            return (new CashierController())->handleWebhook($request);
        }

        return response('Webhook handled', 200);
    }
}

// Custom webhook handler for Cashier events
class CashierWebhookHandler
{
    public function handleCustomerSubscriptionCreated($payload)
    {
        $user = User::where('stripe_id', $payload->data->object->customer)->first();
        
        if ($user) {
            // Cashier automatically creates subscription record
            // Add any custom logic here
            $user->notify(new SubscriptionStartedNotification());
        }
    }

    public function handleInvoicePaymentSucceeded($payload)
    {
        $subscriptionId = $payload->data->object->subscription;
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
        
        if ($subscription) {
            // Update subscription status
            $subscription->update([
                'status' => 'active',
                'last_payment_at' => now(),
            ]);

            // Send payment confirmation
            $subscription->user->notify(new PaymentSuccessfulNotification());
        }
    }

    public function handleCustomerSubscriptionDeleted($payload)
    {
        $subscriptionId = $payload->data->object->id;
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
        
        if ($subscription) {
            // Cashier automatically handles subscription cancellation
            $subscription->user->notify(new SubscriptionCanceledNotification());
        }
    }

    public function handleInvoicePaymentFailed($payload)
    {
        $subscriptionId = $payload->data->object->subscription;
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
        
        if ($subscription) {
            // Handle payment failure
            $subscription->update([
                'status' => 'past_due',
                'last_payment_failed_at' => now(),
            ]);

            // Send payment failure notification
            $subscription->user->notify(new PaymentFailedNotification());
        }
    }
}
```

### Cashier Subscription Model
```php
// Extend Cashier's Subscription model
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    protected $table = 'subscriptions';
    
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPlanNameAttribute()
    {
        return config("cashier.plan.{$this->stripe_plan}.name");
    }

    public function getPlanLimitAttribute()
    {
        return config("cashier.plan.{$this->stripe_plan}.features.active_polls");
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getTrialDaysRemainingAttribute()
    {
        if (!$this->isOnTrial()) {
            return 0;
        }

        return $this->trial_ends_at->diffInDays(now());
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where('stripe_status', 'active')
                  ->orWhere(function ($query) {
                      $query->where('stripe_status', 'trialing')
                            ->where('trial_ends_at', '>', now());
                  });
        });
    }
}
```

## Usage Tracking

### Usage Metrics Model
```php
// Usage metrics tracking
class UsageMetrics extends Model
{
    protected $fillable = [
        'user_id', 'metric_date', 'active_polls_count', 
        'responses_count', 'plan_limit', 'created_at'
    ];

    protected $casts = [
        'metric_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function trackUsage(User $user)
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            [
                'user_id' => $user->id,
                'metric_date' => $today,
            ],
            [
                'active_polls_count' => $user->polls()->where('status', 'active')->count(),
                'responses_count' => $user->polls()->join('poll_responses', 'polls.id', '=', 'poll_responses.poll_id')->count(),
                'plan_limit' => $user->subscription?->plan_limit ?? 10, // Trial limit
            ]
        );
    }
}
```

### Plan Limit Enforcement
```php
// Poll creation with limit checking
class PollCreationService
{
    public function canCreatePoll(User $user)
    {
        $activePolls = $user->polls()->where('status', 'active')->count();
        $planLimit = $user->getPlanLimit();

        if ($activePolls >= $planLimit) {
            return [
                'allowed' => false,
                'message' => "You've reached your plan limit of {$planLimit} active polls. Upgrade your plan to create more.",
                'upgrade_url' => route('billing.plans'),
            ];
        }

        return ['allowed' => true];
    }

    public function enforcePlanLimits(User $user)
    {
        $canCreate = $this->canCreatePoll($user);
        
        if (!$canCreate['allowed']) {
            // Show upgrade prompt
            session()->flash('upgrade_needed', $canCreate['message']);
            return false;
        }

        return true;
    }
}
```

## Billing Page Components

### Plans Selection Page
```php
<?php

use App\Models\User;
use Livewire\Component;

new class extends Component {
    public $user;
    public $availablePlans;
    public $selectedPlan;
    public $billingInterval = 'month';
    public $annualDiscount = 0.2; // 20% discount

    public function mount()
    {
        $this->user = auth()->user();
        $this->availablePlans = $this->getAvailablePlans();
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = $planId;
    }

    public function startTrial()
    {
        // Redirect to trial signup (already in trial)
        return redirect()->route('register');
    }

    public function subscribeToPlan($planId)
    {
        // Use Cashier for checkout creation
        if ($this->user->onTrial()) {
            // Convert trial to paid subscription
            $checkoutUrl = $this->user->newSubscription($planId)
                ->trialDays(0) // End trial immediately
                ->checkout([
                    'success_url' => route('billing.success'),
                    'cancel_url' => route('billing.plans'),
                    'payment_method_types' => ['card', 'apple_pay', 'google_pay'],
                    'allow_promotion_codes' => true,
                ]);
        } else {
            // New subscription
            $checkoutUrl = $this->user->newSubscription($planId)
                ->trialDays(14) // 14-day trial
                ->checkout([
                    'success_url' => route('billing.success'),
                    'cancel_url' => route('billing.plans'),
                    'payment_method_types' => ['card', 'apple_pay', 'google_pay'],
                    'allow_promotion_codes' => true,
                ]);
        }

        return redirect($checkoutUrl);
    }

    public function getAvailablePlans()
    {
        $plans = config('subscriptions.plans');
        
        if ($this->billingInterval === 'year') {
            foreach ($plans as $key => $plan) {
                $plans[$key]['annual_price'] = $plan['amount'] * 12 * (1 - $this->annualDiscount);
                $plans[$key]['price_id'] = str_replace('_monthly', '_yearly', $plan['price_id']);
            }
        }

        return $plans;
    }
};
?>
<!-- Plans selection HTML with Flux UI -->
<div class="max-w-6xl mx-auto space-y-8">
    @if(auth()->user()->onTrial())
        <flux:callout variant="info">
            <flux:callout.content>
                <strong>{{ auth()->user()->trial_days_remaining }} days left in your free trial!</strong>
                Choose a plan below to continue using Antipoll after your trial ends.
            </flux:callout.content>
        </flux:callout>
    @endif

    <!-- Billing Interval Toggle -->
    <div class="flex justify-center mb-8">
        <flux:segmented>
            <flux:segmented.option 
                wire:model="billingInterval" 
                value="month"
            >
                Monthly
            </flux:segmented.option>
            <flux:segmented.option 
                wire:model="billingInterval" 
                value="year"
            >
                Yearly (Save 20%)
            </flux:segmented.option>
        </flux:segmented>
    </div>

    <!-- Plans Grid -->
    <flux:grid class="grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($availablePlans as $planKey => $plan)
            <flux:col>
                <flux:card class="h-full {{ $planKey === 'professional' ? 'ring-2 ring-blue-500' : '' }}">
                    <flux:card.header>
                        <flux:card.title>{{ $plan['name'] }}</flux:card.title>
                        <div class="text-3xl font-bold">
                            ${{ number_format($billingInterval === 'year' ? $plan['annual_price'] / 100 : $plan['amount'] / 100, 2) }}
                            <span class="text-lg text-gray-600">/{{ $billingInterval }}</span>
                        </div>
                        @if($billingInterval === 'year')
                            <flux:badge variant="success">Save 20%</flux:badge>
                        @endif
                    </flux:card.header>
                    <flux:card.content class="space-y-3">
                        <div class="text-2xl font-semibold">{{ $plan['features']['active_polls'] }} Active Polls</div>
                        <div class="text-sm text-gray-600">Unlimited Responses</div>
                        <div class="text-sm">{{ $plan['features']['analytics'] }} Analytics</div>
                        <div class="text-sm">{{ $plan['features']['support'] }} Support</div>
                        
                        @if($planKey === 'professional')
                            <flux:badge variant="primary">Most Popular</flux:badge>
                        @endif
                    </flux:card.content>
                    <flux:card.footer>
                        <flux:button 
                            wire:click="subscribeToPlan('{{ $planKey }}')" 
                            class="w-full"
                            variant="{{ $planKey === 'professional' ? 'primary' : 'outline' }}"
                        >
                            {{ auth()->user()->onTrial() ? 'Choose Plan' : 'Subscribe' }}
                        </flux:button>
                    </flux:card.footer>
                </flux:card>
            </flux:col>
        @endforeach
    </flux:grid>

    <!-- Enterprise CTA -->
    <div class="text-center mt-12">
        <flux:callout>
            <flux:callout.content>
                <h3 class="text-lg font-semibold mb-2">Need a custom solution?</h3>
                <p class="text-gray-600 mb-4">
                    Contact our sales team for enterprise pricing, custom features, and dedicated support.
                </p>
                <flux:button href="{{ route('contact.enterprise') }}" variant="outline">
                    Contact Sales
                </flux:button>
            </flux:callout.content>
        </flux:callout>
    </div>
</div>
```

## Database Schema

### Subscriptions Table
```sql
CREATE TABLE subscriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    stripe_subscription_id VARCHAR(255) UNIQUE NOT NULL,
    plan_type ENUM('starter', 'professional', 'business', 'enterprise') NOT NULL,
    status ENUM('trialing', 'active', 'canceled', 'incomplete', 'incomplete_expired', 'past_due', 'unpaid') NOT NULL,
    trial_ends_at TIMESTAMP NULL,
    current_period_start TIMESTAMP NOT NULL,
    current_period_end TIMESTAMP NOT NULL,
    canceled_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    stripe_price_id VARCHAR(255) NOT NULL,
    amount INT NOT NULL,
    currency VARCHAR(3) NOT NULL,
    interval ENUM('day', 'week', 'month', 'year') NOT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_stripe_subscription_id (stripe_subscription_id),
    INDEX idx_status (status),
    INDEX idx_current_period_end (current_period_end)
);
```

### Subscription Items Table
```sql
CREATE TABLE subscription_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    subscription_id BIGINT UNSIGNED NOT NULL,
    stripe_price_id VARCHAR(255) NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    amount INT NOT NULL,
    currency VARCHAR(3) NOT NULL,
    interval ENUM('day', 'week', 'month', 'year') NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE,
    INDEX idx_subscription_id (subscription_id),
    INDEX idx_stripe_price_id (stripe_price_id)
);
```

### Usage Metrics Table
```sql
CREATE TABLE usage_metrics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    metric_date DATE NOT NULL,
    active_polls_count INT DEFAULT 0,
    responses_count INT DEFAULT 0,
    plan_limit INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, metric_date),
    INDEX idx_metric_date (metric_date)
);
```

## Security & Compliance

### Payment Security
- **PCI DSS Compliance**: Stripe handles all PCI compliance requirements
- **3D Secure**: Strong Customer Authentication (SCA) support for European cards
- **Tokenization**: Payment methods stored securely via Stripe tokens
- **Encryption**: All data encrypted in transit and at rest

### Subscription Security
- **Webhook Verification**: All Stripe webhooks verified using signature
- **Access Control**: Role-based access to billing features
- **Audit Logging**: Complete audit trail of all billing actions
- **Rate Limiting**: Prevent abuse of subscription management features

### Compliance
- **GDPR Compliance**: Right to data deletion and portability
- **CCPA Compliance**: California privacy law compliance
- **Tax Compliance**: Automatic tax calculation and reporting
- **Accessibility**: WCAG 2.1 AA compliance for billing interface

## Testing Strategy

### Subscription Testing
- **Trial Flow**: Test complete 14-day trial experience
- **Plan Upgrades**: Test upgrade flows and proration
- **Plan Downgrades**: Test downgrade and data retention
- **Payment Failures**: Test dunning management and recovery
- **Webhook Processing**: Test all Stripe webhook events

### Integration Testing
- **Stripe Test Mode**: Use Stripe test environment for development
- **Webhook Testing**: Use Stripe CLI for webhook testing
- **Payment Methods**: Test various payment method types
- **Currency Support**: Test multi-currency billing scenarios

This specification provides a comprehensive foundation for implementing a robust SaaS subscription system with Stripe integration for the Antipoll platform.