# Billing and Subscription Specification

## Overview
The application implements a subscription-based billing system with middleware protection and organization-level subscription management. Organizations are the billable entity, not individual users.

## Subscription Middleware (`EnsureUserIsSubscribed`)

### Features
- **Route Protection**: Middleware protects premium features
- **Organization-Based**: Checks subscription status of current organization
- **Redirect Handling**: Redirects unsubscribed users to subscription-required page

### Test Cases
- Redirects unsubscribed users from protected routes
- Allows access for subscribed organizations
- Proper middleware integration with Laravel routing

### Protected Routes
- `/dashboard` - Main application dashboard
- Other premium features (as defined in route configuration)

## Subscription Required Page (`pages::billing.subscription-required`)

### Features
- **Organization Switching**: Allows switching between organizations
- **Smart Redirects**: Redirects to dashboard if switching to subscribed organization
- **Access Control**: Only shows organizations user owns or is member of

### Test Cases
- Switching to subscribed organization redirects to dashboard
- Switching to non-subscribed organization stays on page
- Allows switching to member organizations
- Prevents switching to unauthorized organizations

### User Experience
- Clear messaging about subscription requirements
- Organization switching capabilities
- Proper authorization checks

## Billing Portal (`pages::billing.billing-portal`)

### Features
- **Subscription Management**: Interface for managing subscriptions
- **Access Control**: Only accessible to subscribed users
- **Integration**: Likely integrates with payment provider (Stripe/Paddle)

### Test Cases
- Redirects unsubscribed users to subscription-required page
- Proper access control enforcement

### Current Limitations
- Test coverage is minimal
- Payment provider integration not fully tested
- Subscription management flows need expanded testing

## Subscription Model

### Organization Subscription
- **Organization-Level**: Subscriptions are tied to organizations, not users
- **Current Organization**: Subscription checks use user's current organization
- **Personal Organizations**: Each user has a personal organization that can be subscribed

### Factory Support
- `withPersonalOrganizationAndSubscription()` - Creates user with subscribed org
- `withSubscription()` - Adds subscription to organization
- `withPersonalOrganization()` - Creates user with personal org

## User Experience Flow

### Unsubscribed User Journey
1. User attempts to access protected route
2. Middleware checks current organization subscription
3. If unsubscribed, redirect to subscription-required page
4. User can switch organizations (if they have subscribed ones)
5. User completes subscription process
6. User regains access to protected features

### Multi-Organization Support
- Users can have multiple organizations with different subscription statuses
- Current organization determines subscription access
- Smart switching between organizations based on subscription status

## Security Considerations

### Access Control
- Middleware-based protection ensures consistent enforcement
- Organization-level subscription prevents unauthorized access
- Proper authorization checks on subscription management

### Data Isolation
- Subscription status is properly scoped to organizations
- Users cannot access other organizations' subscription data
- Current organization switching maintains security boundaries

## Testing Strategy

### Current Coverage
- Middleware protection testing
- Subscription required page functionality
- Organization switching with subscription awareness
- Basic billing portal access control

### Recommended Expansion
- Payment provider integration testing
- Subscription lifecycle testing (creation, cancellation, renewal)
- Webhook handling for payment events
- Edge cases and error scenarios
- Performance testing for subscription checks

## Configuration

### Middleware Registration
- Registered in Laravel's middleware system
- Applied to routes requiring subscription
- Configurable for different subscription tiers

### Route Protection
- Applied via route groups or individual routes
- Integrates with Laravel's authentication system
- Proper error handling and redirects

## Dependencies
- Laravel's middleware system
- Payment provider SDK (Stripe/Paddle - not explicitly tested)
- Organization model relationships
- User authentication system