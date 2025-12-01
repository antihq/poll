# Subscription Billing System Specification

## Overview
This specification covers SaaS subscription billing system, 14-day free trial management, and Stripe payment processing for Antipoll platform.

## Pricing Model

### Free Trial
- **14-Day Trial**: Full access to all features
- **No Credit Card Required**: Start immediately without payment
- **All Features Included**: Unlimited poll creation during trial
- **Trial Expiration**: Automatic downgrade to free tier after 14 days
- **Trial Reminders**: Email notifications at 7 days, 3 days, and 1 day before expiration

### Subscription Plans

#### Starter Plan - $29/month
- **Active Polls**: Up to 10 concurrent active polls
- **Responses**: Unlimited responses per month
- **Analytics**: Basic analytics and reporting
- **Email Integration**: All email platforms supported
- **Support**: Email support

#### Professional Plan - $79/month
- **Active Polls**: Up to 50 concurrent active polls
- **Responses**: Unlimited responses per month
- **Analytics**: Advanced analytics with custom reports
- **Email Integration**: All email platforms + priority processing
- **Support**: Priority email support
- **Features**: A/B testing, advanced segmentation

#### Business Plan - $199/month
- **Active Polls**: Unlimited concurrent active polls
- **Responses**: Unlimited responses per month
- **Analytics**: Enterprise analytics with API access
- **Email Integration**: All platforms + custom integrations
- **Support**: Dedicated account manager
- **Features**: White-label options, custom domains
- **Team Members**: Up to 10 team seats

#### Enterprise Plan - Custom Pricing
- **Active Polls**: Unlimited
- **Responses**: Unlimited
- **Analytics**: Custom analytics solutions
- **Email Integration**: Custom platform development
- **Support**: 24/7 phone + dedicated team
- **Features**: Full white-label, SLA guarantees
- **Team Members**: Unlimited team seats
- **Compliance**: SOC 2, GDPR, HIPAA options
5 Poll Credits   - $12.99  (Save 13%)
10 Poll Credits  - $24.99  (Save 17%)
25 Poll Credits  - $59.99  (Save 20%)
50 Poll Credits  - $109.99 (Save 27%)
100 Poll Credits - $199.99 (Save 33%)
```

## Subscription System Architecture

### Subscription Management
- **Trial Period**: 14-day free trial with full feature access
- **Automatic Billing**: Monthly recurring charges via Stripe
- **Plan Changes**: Prorated upgrades/downgrades
- **Cancellation**: Immediate cancellation with access until period end
- **Reactivation**: Resume subscription without losing data

### Subscription Tracking
```sql
subscriptions:
- id, user_id, stripe_subscription_id
- plan_type, status, trial_ends_at
- current_period_start, current_period_end
- created_at, updated_at, canceled_at

subscription_items:
- id, subscription_id, stripe_price_id
- plan_name, amount, currency
- quantity, created_at, updated_at

usage_metrics:
- id, user_id, metric_date
- active_polls_count, responses_count
- plan_limit, created_at
```

### Plan Limits Enforcement
- **Active Poll Limits**: Enforced at poll creation time
- **Usage Tracking**: Real-time monitoring of poll usage
- **Limit Notifications**: Warnings when approaching limits
- **Graceful Degradation**: Read-only access when limits exceeded
- **Upgrade Prompts**: In-app upgrade suggestions when needed

## Payment Processing with Cashier

### Payment Methods
- **Credit/Debit Cards**: Visa, Mastercard, American Express
- **Digital Wallets**: Apple Pay, Google Pay
- **Bank Transfers**: ACH for enterprise customers
- **International**: Multiple currency support

### Cashier Stripe Integration
- **Primary Processor**: Cashier handles Stripe integration
- **PCI Compliance**: Cashier manages secure payment handling
- **Multi-Currency**: Support for USD, EUR, GBP, CAD
- **Tax Calculation**: Automatic tax calculation via Stripe
- **3D Secure**: Cashier handles SCA automatically
- **Payment Methods**: Stored securely via Cashier

### Cashier Subscription Checkout Flow
1. User selects subscription plan
2. Cashier creates Stripe Checkout Session
3. Redirects to Stripe-hosted payment page
4. User enters payment information on Stripe
5. Stripe processes payment and creates subscription
6. Cashier webhook confirms subscription activation
7. User account updated with subscription status
8. Welcome email sent with plan details

### Cashier Trial Conversion Flow
1. Trial user clicks "Choose Plan"
2. Cashier creates checkout with trialDays(0)
3. Redirects to Stripe-hosted payment page
4. Payment processed and subscription activated
5. Trial period ends immediately
6. New subscription period begins
7. User retains all existing polls and data

### Cashier Payment Method Management
```php
// Update payment method using Cashier
$user->updateDefaultPaymentMethod($paymentMethodId);

// Add new payment method
$user->addPaymentMethod($paymentMethodId);

// Delete payment method
$user->deletePaymentMethod($paymentMethodId);

// Get all payment methods
$paymentMethods = $user->paymentMethods();
```

### Cashier Invoice Management
```php
// Download invoice using Cashier
$invoice = $user->findInvoice($invoiceId);
return $user->downloadInvoice($invoiceId);

// List all invoices
$invoices = $user->invoices();

// Get upcoming invoice
$upcomingInvoice = $user->upcomingInvoice();
```

## User Billing Dashboard

### Subscription Overview
- **Current Plan**: Active subscription plan and status
- **Trial Status**: Days remaining in free trial
- **Usage Metrics**: Active polls vs plan limits
- **Billing Cycle**: Current period dates and next charge
- **Payment Method**: Saved payment methods and billing info

### Subscription Management Features
- **Plan Management**: Upgrade, downgrade, or cancel subscription
- **Usage Analytics**: Poll usage over time with limits visualization
- **Payment Methods**: Add, update, or remove payment methods
- **Billing History**: Download invoices and payment receipts
- **Team Management**: Add/remove team seats (Business+ plans)

### Billing Interface Components
```php
<?php

use App\Models\User;
use App\Models\Subscription;
use Livewire\Component;

new class extends Component {
    public User $user;
    public $subscription;
    public $usageMetrics;
    public $billingHistory;
    public $availablePlans;
    public $trialDaysRemaining;
    
    public function mount()
    {
        $this->refreshData();
    }
    
    public function upgradePlan($planId)
    {
        // Create Stripe Checkout Session for plan upgrade
        $checkoutSession = $this->user->createPlanUpgradeCheckout($planId);
        return redirect($checkoutSession->url);
    }
    
    public function cancelSubscription()
    {
        // Cancel subscription with access until period end
        $this->user->subscription->cancel();
        $this->refreshData();
    }
    
    public function refreshData()
    {
        $this->subscription = $this->user->activeSubscription();
        $this->usageMetrics = $this->user->getCurrentUsageMetrics();
        $this->billingHistory = $this->user->billingHistory()->get();
        $this->availablePlans = $this->getAvailablePlans();
        $this->trialDaysRemaining = $this->user->getTrialDaysRemaining();
    }
};
?>
<!-- Billing dashboard HTML here -->
```

### Billing Page Components
```php
// Page components for billing
php artisan make:livewire pages::billing.dashboard
php artisan make:livewire pages::billing.plans
php artisan make:livewire pages::billing.payment-methods
php artisan make:livewire pages::billing.history
php artisan make:livewire pages::billing.invoices
```

### Route Definitions for Billing Pages
```php
// routes/web.php
Route::livewire('/billing', 'pages::billing.dashboard')->name('billing.dashboard');
Route::livewire('/billing/plans', 'pages::billing.plans')->name('billing.plans');
Route::livewire('/billing/payment-methods', 'pages::billing.payment-methods')->name('billing.payment-methods');
Route::livewire('/billing/history', 'pages::billing.history')->name('billing.history');
Route::livewire('/billing/invoices', 'pages::billing.invoices')->name('billing.invoices');
```

### Billing Page Components
```php
// Page components for billing
php artisan make:livewire pages::billing.dashboard
php artisan make:livewire pages::billing.purchase
php artisan make:livewire pages::billing.history
php artisan make:livewire pages::billing.invoices
```

### Route Definitions for Billing Pages
```php
// routes/web.php
Route::livewire('/billing', 'pages::billing.dashboard')->name('billing.dashboard');
Route::livewire('/billing/purchase', 'pages::billing.purchase')->name('billing.purchase');
Route::livewire('/billing/history', 'pages::billing.history')->name('billing.history');
Route::livewire('/billing/invoices', 'pages::billing.invoices')->name('billing.invoices');
```

### Cashier Billing Components
```blade
<!-- Subscription status with Cashier and Flux UI -->
<flux:grid>
    <flux:col>
        <flux:card>
            <flux:card.header>Current Plan</flux:card.header>
            <flux:card.content>
                <div class="text-3xl font-bold">{{ $subscription->name ?? 'Free Trial' }}</div>
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
                <div class="text-3xl font-bold">{{ $usageMetrics->active_polls_count }} / {{ $usageMetrics->plan_limit }}</div>
                <div class="text-sm text-gray-600">
                    Active polls this month
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($usageMetrics->active_polls_count / $usageMetrics->plan_limit) * 100 }}%"></div>
                </div>
            </flux:card.content>
        </flux:card>
    </flux:col>
</flux:grid>

<!-- Available plans with Cashier and Flux UI -->
<flux:card>
    <flux:card.header>Available Plans</flux:card.header>
    <flux:card.content>
        <flux:grid class="grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($availablePlans as $planKey => $plan)
                <flux:col>
                    <flux:card class="h-full {{ $planKey === 'professional' ? 'ring-2 ring-blue-500' : '' }}">
                        <flux:card.header>
                            <flux:card.title>{{ $plan['name'] }}</flux:card.title>
                            <div class="text-2xl font-bold">
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
    </flux:card.content>
</flux:card>
```

## Enterprise Features

### Team Management
- **Team Seats**: Add/remove team members based on plan limits
- **Role-Based Access**: Admin, Member, and Viewer roles
- **Shared Polls**: Collaborative poll creation and management
- **Department Organization**: Organize teams by departments
- **Activity Logging**: Track team member actions and changes

### Custom Pricing
- **Volume Discounts**: Custom pricing for high-volume organizations
- **Enterprise Plans**: Tailored solutions with custom features
- **Non-Profit Discounts**: 50% discount for qualified non-profits
- **Educational Pricing**: 40% discount for educational institutions
- **Annual Billing**: 20% discount for annual prepayment

### Advanced Features
- **API Access**: Full REST API for custom integrations
- **Webhook Support**: Real-time notifications to external systems
- **SSO Integration**: SAML and OAuth 2.0 single sign-on
- **Custom Domains**: White-label with custom branding
- **SLA Guarantees**: 99.9% uptime and support response times
- **Compliance**: SOC 2 Type II, GDPR, HIPAA compliance options
- **Dedicated Support**: 24/7 phone and dedicated account manager

## Revenue Management

### Subscription Lifecycle
- **Trial Conversion**: Automated trial-to-paid conversion flow
- **Churn Reduction**: Automated win-back campaigns for cancellations
- **Expansion Revenue**: Upsell prompts based on usage patterns
- **Revenue Recognition**: Monthly recurring revenue tracking
- **Customer Lifetime Value**: CLV analytics and optimization

### Billing Operations
- **Dunning Management**: Automated failed payment recovery
- **Tax Compliance**: Automatic tax calculation and reporting
- **Multi-Currency**: Global billing with currency conversion
- **Refund Policy**: 30-day money-back guarantee for new customers
- **Proration**: Fair billing for mid-cycle plan changes
- **Invoice Management**: Automated invoicing and payment reminders

## Technical Implementation

### Backend Architecture
```php
// Credit management service
class CreditService
{
    public function purchaseCredits(User $user, Package $package): Transaction
    {
        // Process payment
        // Add credits to account
        // Send confirmation
        // Log transaction
    }
    
    public function consumeCredits(User $user, int $amount): bool
    {
        // Check sufficient balance
        // Deduct credits
        // Log consumption
        // Return success
    }
}
```

### Payment Processing
```php
// Stripe integration
class PaymentProcessor
{
    public function processPayment(Package $package, PaymentMethod $method): PaymentResult
    {
        // Create Stripe payment intent
        // Process payment
        // Handle success/failure
        // Return result
    }
}
```

### Credit Tracking
```php
// Credit transaction logging
class CreditTransaction
{
    public function log(User $user, int $amount, string $type, string $description): void
    {
        // Create transaction record
        // Update user balance
        // Send notifications
    }
}
```

## Security & Compliance with Cashier

### Payment Security
- **PCI DSS Compliance**: Cashier handles all PCI compliance through Stripe
- **Tokenization**: Secure storage of payment methods via Cashier
- **Fraud Detection**: Stripe Radar integration through Cashier
- **Data Encryption**: All data encrypted in transit and at rest
- **3D Secure**: Cashier handles Strong Customer Authentication (SCA)

### Subscription Security
- **Webhook Verification**: Cashier handles Stripe webhook verification
- **Access Control**: Role-based access to billing features
- **Audit Logging**: Complete audit trail of all billing actions
- **Data Minimization**: Only collect necessary payment information
- **Payment Method Security**: Secure payment method storage and management

### Compliance & Regulation
- **GDPR Compliance**: Right to deletion and data export via Cashier
- **CCPA Compliance**: California privacy law compliance
- **Tax Compliance**: Automatic tax calculation via Stripe and Cashier
- **SOC 2**: Security compliance for enterprise customers
- **Accessibility**: WCAG 2.1 AA compliance for billing interface
- **PCI DSS Level 1**: Cashier maintains PCI compliance through Stripe

### Cashier Security Features
- **Secure Checkout**: Cashier's hosted checkout pages
- **Payment Method Storage**: Secure tokenization via Cashier
- **Subscription Management**: Secure subscription lifecycle management
- **Invoice Handling**: Secure invoice generation and delivery
- **Tax Handling**: Automatic tax calculation and collection

## User Experience with Cashier

### Trial Onboarding Flow
- User signs up for 14-day free trial
- Cashier automatically sets trial period
- Full access to all features during trial
- Guided tutorial for first poll creation
- Trial progress indicators in dashboard
- Cashier handles trial expiration gracefully

### Subscription Management with Cashier
- **Plan Comparison**: Clear side-by-side plan comparison
- **Upgrade Prompts**: Contextual upgrade suggestions based on usage
- **Downgrade Options**: Graceful plan downgrades with proration
- **Cancellation Flow**: Simple cancellation with access until period end
- **Reactivation**: Easy subscription reactivation via Cashier
- **Payment Method Updates**: Secure payment method management

### Customer Support
- **Billing Support**: Dedicated support for Cashier subscription issues
- **Plan Changes**: Assistance with plan upgrades/downgrades
- **Payment Problems**: Help with failed payments via Cashier
- **Usage Guidance**: Recommendations for optimal plan selection
- **Enterprise Support**: Dedicated account manager for business plans
- **Invoice Support**: Easy invoice download and management

### Cashier-Specific Features
- **Secure Checkout**: Cashier's Stripe-hosted checkout pages
- **Trial Management**: Automatic trial period handling
- **Subscription Swapping**: Easy plan changes with proration
- **Invoice Access**: Direct invoice download and management
- **Payment Method Security**: Secure payment method storage and updates

## Analytics & Reporting

### Revenue Analytics
- **Revenue Tracking**: Real-time revenue monitoring
- **Customer Lifetime Value**: CLV analysis
- **Conversion Rates**: Free to paid user conversion
- **Churn Analysis**: User retention and churn metrics

### Usage Analytics
- **Credit Consumption**: How users spend credits
- **Purchase Patterns**: When and how users buy credits
- **Feature Usage**: Which features drive credit consumption
- **User Segmentation**: Analysis by user type and behavior

## International Considerations

### Multi-Currency Support
- **Currency Detection**: Automatic currency detection
- **Exchange Rates**: Real-time exchange rate updates
- **Local Pricing**: Region-specific pricing
- **Tax Handling**: Country-specific tax calculation

### Payment Methods
- **Local Methods**: Region-specific payment options
- **Bank Transfers**: Direct bank payment options
- **Mobile Payments**: Local mobile payment integration
- **Invoice Billing**: Invoice-based payment for enterprises