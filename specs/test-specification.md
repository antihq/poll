# Test Specification and Strategy

## Overview
This application uses Pest PHP as its primary testing framework with comprehensive test coverage for authentication, organization management, billing, and user settings.

## Testing Framework

### Pest PHP
- **Primary Framework**: Pest PHP for all test files
- **Syntax**: Uses `it()` and `test()` functions for test cases
- **Assertions**: Laravel's built-in assertion methods
- **Database**: Uses `RefreshDatabase` trait for database isolation

### Test Structure
- **Feature Tests**: `/tests/Feature/` - HTTP endpoint and workflow testing
- **Unit Tests**: `/tests/Unit/` - Isolated unit testing
- **Component Tests**: Co-located with Livewire components

## Test Categories

### Authentication Tests
**Location**: `/tests/Feature/Auth/EmailVerificationTest.php`

#### Test Cases
- OTP login for unverified users
- Email verification independence from OTP system
- Backward compatibility with existing users

### Billing Tests
**Location**: `/tests/Feature/Billing/EnsureUserIsSubscribedTest.php`

#### Test Cases
- Middleware protection for unsubscribed users
- Redirect behavior to subscription-required page
- Subscription status validation

### Dashboard Tests
**Location**: `/tests/Feature/DashboardTest.php`

#### Test Cases
- Guest redirect to login
- Authenticated user access
- Proper HTTP status codes

### Organization Invitation Tests
**Location**: `/tests/Feature/OrganizationInvitationAcceptTest.php`

#### Test Cases
- Invitation acceptance workflow
- Organization membership assignment
- Current organization switching
- Invitation cleanup after acceptance

### Livewire Component Tests

#### Organization Creation
**Location**: `/resources/views/livewire/organizations/⚡create.test.php`

- Valid organization creation
- Validation error handling
- Guest access prevention

#### Organization Dropdown
**Location**: `/resources/views/livewire/⚡organizations-dropdown.test.php`

- Organization switching
- Member organization access
- Unauthorized access prevention

#### Authentication Pages
**Login**: `/resources/views/pages/auth/⚡login.test.php`
- Login screen rendering
- OTP sending and validation
- Authentication success/failure
- Security features (email enumeration protection)

**Register**: `/resources/views/pages/auth/⚡register.test.php`
- Registration screen rendering
- User creation and OTP sending
- Registration completion with OTP
- Personal organization creation
- Email validation

#### Billing Pages
**Billing Portal**: `/resources/views/pages/billing/⚡billing-portal.test.php`
- Access control for unsubscribed users
- Redirect behavior

**Subscription Required**: `/resources/views/pages/billing/⚡subscription-required.test.php`
- Organization switching with subscription awareness
- Authorization checks

#### Organization Settings
**General Settings**: `/resources/views/pages/organizations/settings/⚡general.test.php`
- Organization name editing
- Authorization for owners only
- Validation requirements

**Members Management**: `/resources/views/pages/organizations/settings/⚡members.test.php`
- Member addition and removal
- Invitation system
- Authorization and security
- Current organization management

#### User Settings
**Profile**: `/resources/views/pages/settings/⚡profile.test.php`
- Profile information updates
- Email verification handling
- Access control

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
```

### Database Testing
```php
// Using RefreshDatabase trait
uses(RefreshDatabase::class);

// Factory usage
$user = User::factory()->withPersonalOrganization()->create();
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

### Factory Methods
- `withPersonalOrganization()` - Creates user with personal org
- `withPersonalOrganizationAndSubscription()` - Creates subscribed user
- `withSubscription()` - Adds subscription to organization
- `unverified()` - Creates unverified user

## Test Coverage Analysis

### Well-Covered Areas
- Authentication flows (login, registration, OTP)
- Organization management (CRUD, membership, invitations)
- Billing middleware and subscription checks
- User profile management

### Areas for Expansion
- Error handling and edge cases
- Performance testing
- Integration testing with external services
- Security testing beyond basic authorization
- Accessibility testing

### Missing Tests
- Appearance settings (referenced but not implemented)
- Advanced billing scenarios (webhooks, failures)
- Email template testing
- File upload handling (if applicable)

## Testing Best Practices

### Test Organization
- Co-located tests with Livewire components
- Clear, descriptive test names
- Proper setup and teardown
- Isolated test execution

### Data Management
- Factory usage over manual creation
- Database cleanup between tests
- Proper relationship handling

### Assertion Strategy
- Specific assertions over generic ones
- Both positive and negative test cases
- Proper HTTP status code validation
- Database state verification

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

### Additional Tools
- Notification faking
- File system mocking
- HTTP client mocking
- Time manipulation (if needed)