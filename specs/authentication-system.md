# Authentication System Specification

## Overview
The application uses a One-Time Password (OTP) based authentication system that replaces traditional email verification. Users authenticate using temporary passwords sent to their email addresses.

## Login System (`pages::auth.login`)

### Features
- **Email Input**: User enters email address
- **OTP Generation**: System generates and sends OTP to user's email
- **OTP Verification**: User enters OTP to complete authentication
- **Security**: Does not reveal if user exists when sending OTP (always shows OTP form)

### Test Cases
- Renders login screen successfully
- Sends OTP notification for valid email addresses
- Shows OTP form after email submission
- Authenticates users with valid OTP
- Rejects authentication with invalid OTP
- Maintains security by not revealing user existence
- Logs out authenticated users properly

### Security Features
- OTP-based authentication eliminates password storage
- Email enumeration protection during OTP request
- Automatic OTP expiration (handled by OneTimePassword package)

## Registration System (`pages::auth.register`)

### Features
- **Two-Step Process**: 
  1. User provides name and email
  2. System sends OTP for verification
- **Personal Organization**: Automatically creates personal organization for new users
- **Email Verification**: OTP verification serves as email verification

### Test Cases
- Renders registration screen successfully
- Creates user and sends OTP with valid data
- Completes registration with valid OTP
- Creates personal organization automatically
- Rejects registration with invalid OTP
- Validates unique email constraint
- Sets email as verified after successful OTP verification

### User Creation Flow
1. User submits name and email
2. System creates unverified user record
3. OTP is generated and sent via email notification
4. User enters OTP to verify email
5. User becomes verified and is logged in
6. Personal organization is created and set as current

## Email Verification System

### Legacy vs New System
- **Legacy**: Traditional email verification links
- **Current**: OTP-based verification during registration/login
- **Backward Compatibility**: Existing users can login with OTP regardless of verification status

### Test Cases
- OTP login works for unverified users
- Email verification status is preserved during profile updates
- Email verification is automatically set during registration completion

## Security Considerations

### OTP Security
- Time-limited passwords (default expiration)
- Single-use passwords
- Secure generation using OneTimePassword package

### Email Enumeration Protection
- Same response for existing and non-existing emails during OTP request
- OTP form always shown after email submission

### Session Management
- Proper logout functionality
- Session invalidation on logout
- Authentication state properly maintained

## Dependencies
- `spatie/one-time-passwords`: OTP generation and validation
- Laravel's built-in authentication system
- Laravel's notification system for email delivery