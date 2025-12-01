# Test Specification and Strategy

## Overview
This application uses Pest PHP as its primary testing framework with comprehensive test coverage for authentication, organization management, billing, and user settings.

## Testing Framework

### Pest PHP
- **Primary Framework**: Pest PHP for all test files
- **Syntax**: Uses `it()` functions exclusively for test cases (standardized from mixed `it()`/`test()`)
- **Assertions**: Laravel's built-in assertion methods
- **Database**: Uses `RefreshDatabase` trait for database isolation

### Test Structure
- **Feature Tests**: `/tests/Feature/Specs/` - Consolidated HTTP endpoint and workflow testing
- **Unit Tests**: `/tests/Unit/` - Isolated unit testing
- **Spec References**: Each test file includes comment referencing corresponding specification document

## Test Categories

### Authentication Tests
**Location**: `/tests/Feature/Specs/AuthenticationTest.php` & `/tests/Feature/Specs/AuthenticationAndDashboardTest.php`

#### Test Cases
- OTP login for unverified users
- Email verification independence from OTP system
- Backward compatibility with existing users
- Login screen rendering and OTP flows
- Registration with personal organization creation
- Email enumeration protection
- Logout functionality

### Billing Tests
**Location**: `/tests/Feature/Specs/BillingPortalTest.php` & `/tests/Feature/Specs/EnsureUserIsSubscribedTest.php`

#### Test Cases
- Middleware protection for unsubscribed users
- Redirect behavior to subscription-required page
- Subscription status validation
- Billing portal access control
- Organization switching with subscription awareness

### Dashboard Tests
**Location**: `/tests/Feature/Specs/DashboardTest.php` & `/tests/Feature/Specs/AuthenticationAndDashboardTest.php`

#### Test Cases
- Guest redirect to login
- Authenticated user access
- Proper HTTP status codes
- Home page access

### Organization Tests
**Location**: `/tests/Feature/Specs/OrganizationCreateTest.php`, `/tests/Feature/Specs/OrganizationGeneralSettingsTest.php`, `/tests/Feature/Specs/OrganizationMembersSettingsTest.php`, `/tests/Feature/Specs/OrganizationInvitationAcceptTest.php`

#### Test Cases
- Organization creation and validation
- Organization switching and dropdown functionality
- Organization settings and authorization
- Member management and invitations
- Invitation acceptance workflow
- Current organization management
- Access control for owners vs members

## Testing Patterns

### Authentication Patterns
```php
// Guest testing
$response = $this->get('/protected-route');
$response->assertRedirect('/login');

// Authenticated testing
$user = User::factory()->create();
actingAs($user)->get('/protected-route')->assertOk();
```

### Livewire Testing
```php
// Component testing
Livewire::test('component-name')
    ->set('property', 'value')
    ->call('method')
    ->assertHasNoErrors();

// Page component testing with authentication
Livewire::actingAs($user)
    ->test('pages::component-name')
    ->set('property', 'value')
    ->call('method')
    ->assertRedirect()
    ->assertSessionHas('success');

// Real-time validation testing
Livewire::actingAs($user)
    ->test('pages::polls.create')
    ->set('question', '')
    ->assertHasErrors(['question']);
```

### Database Testing
```php
// Using RefreshDatabase trait
uses(RefreshDatabase::class);

// Factory usage
$user = User::factory()->withPersonalOrganization()->create();

// Model assertions (preferred over assertDatabaseHas)
$poll = Poll::first();
expect($poll)->not->toBeNull();
expect($poll->status)->toBe('draft');

// Relationship testing
$options = $poll->options()->orderBy('sort_order')->get();
expect($options)->toHaveCount(3);
```

### Notification Testing
```php
// Notification faking
Notification::fake();
Notification::assertSentTo($user, NotificationClass::class);
```

## Test Data Management

### Factories
- **User Factory**: With/without organizations and subscriptions
- **Organization Factory**: With subscription support
- **Organization Invitation Factory**: For testing invitations
- **Poll Factory**: For creating poll test data with various configurations
- **Poll Option Factory**: For creating poll answer options with different settings

### Factory Methods
- `withPersonalOrganization()` - Creates user with personal org
- `withPersonalOrganizationAndSubscription()` - Creates subscribed user
- `withSubscription()` - Adds subscription to organization
- `unverified()` - Creates unverified user

## Testing Best Practices

### Test Organization
- **Consolidated Structure**: All feature tests in `/tests/Feature/Specs/` directory
- **Spec References**: Each test file includes comment referencing corresponding specification
- **Clear, descriptive test names**: Using `it()` syntax consistently
- **Proper setup and teardown**: Using `RefreshDatabase` trait and factory patterns
- **Isolated test execution**: Each test runs independently with clean database state

### Data Management
- Factory usage over manual creation
- Database cleanup between tests
- Proper relationship handling

### Assertion Strategy
- Specific assertions over generic ones
- Both positive and negative test cases
- Proper HTTP status code validation
- Database state verification using model assertions (preferred over assertDatabaseHas)
- Real-time validation testing with Livewire components
- Component state and interaction testing

## Security Testing

### Current Coverage
- Authorization checks for all protected resources
- Input validation testing
- Email enumeration protection
- Organization access control

### Recommended Security Tests
- SQL injection attempts
- XSS protection
- CSRF token validation
- Rate limiting
- Session security

## Performance Testing

### Current State
- No explicit performance tests
- Basic response time assertions

### Recommendations
- Database query optimization testing
- Load testing for critical paths
- Memory usage monitoring
- Caching effectiveness testing

## Continuous Integration

### GitHub Actions
- **Lint Workflow**: Code quality checks
- **Tests Workflow**: Automated test execution
- **Deploy Workflow**: Deployment automation

### Test Execution
- Parallel test execution
- Database setup and teardown
- Environment configuration
- Coverage reporting

## Testing Dependencies

### Core Dependencies
- Pest PHP testing framework
- Laravel's testing utilities
- Faker for test data generation
- RefreshDatabase trait
- Livewire testing utilities

### Additional Tools
- Notification faking
- File system mocking
- HTTP client mocking
- Time manipulation (if needed)
- Model assertions with expect() syntax
- Real-time validation testing for Livewire components
