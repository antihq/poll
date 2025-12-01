# Organization Management Specification

## Overview
The system supports multi-tenant organizations with role-based access control, invitation systems, and subscription management.

## Organization Creation (`organizations.create`)

### Features
- **Organization Creation**: Authenticated users can create new organizations
- **Validation**: Requires valid organization name
- **Ownership**: Creator becomes organization owner
- **Current Organization**: New organization is set as user's current organization

### Test Cases
- Creates organization with valid data
- Shows validation errors for missing/invalid names
- Prevents guests from creating organizations
- Sets organization as current after creation

### Access Control
- Only authenticated users can create organizations
- Organization name is required
- No duplicate name enforcement at database level

## Organization Switching (`organizations-dropdown`)

### Features
- **Dropdown Interface**: User can switch between organizations
- **Membership Support**: Can switch to owned organizations or member organizations
- **Current Organization Tracking**: Maintains user's current organization context
- **Security**: Prevents switching to unauthorized organizations

### Test Cases
- Allows switching between owned organizations
- Allows switching to member organizations
- Prevents switching to unauthorized organizations
- Redirects to dashboard after successful switch

### Security Rules
- Users can only switch to organizations they own or are members of
- Forbidden response for unauthorized access attempts
- Current organization properly updated after switch

## Organization Settings - General (`pages::organizations.settings.general`)

### Features
- **Organization Name Editing**: Owners can edit organization name
- **Validation**: Name cannot be empty
- **Authorization**: Only owners can access settings

### Test Cases
- Owners can edit organization name
- Validates required name field
- Forbids non-owners from editing
- Returns successful response for authorized access
- Forbids organization members from accessing settings

### Authorization Rules
- Only organization owners can access general settings
- Members cannot access organization settings
- Proper authorization checks on all operations

## Organization Settings - Members (`pages::organizations.settings.members`)

### Features
- **Member Management**: Owners can add, remove, and manage members
- **Invitation System**: Email-based invitations for new members
- **Invitation Management**: Revoke pending invitations
- **Current Organization Handling**: Updates current organization when member is removed

### Test Cases
- Removes members and updates their current organization
- Allows owners to remove members
- Forbids removing members from other organizations
- Forbids non-owners from accessing member management
- Sends email invitations
- Validates email requirements and uniqueness
- Allows revoking pending invitations
- Forbids revoking invitations from other organizations

### Member Management Features
- **Add Members**: Send email invitations
- **Remove Members**: Remove existing members
- **Invitation System**: 
  - Email-based invitations
  - Unique email constraint per organization
  - Revoke pending invitations
- **Current Organization**: Automatically switches removed member's current organization

### Security Controls
- Only owners can manage members
- Cannot remove members from organizations you don't own
- Cannot access member management without ownership
- Email validation and uniqueness enforcement

## Organization Invitations

### Invitation Flow
1. Owner sends invitation to email address
2. System creates invitation record
3. Email notification sent to invitee
4. Invitee accepts invitation via signed URL
5. User becomes organization member
6. Invitation record is deleted

### Acceptance Process (`OrganizationInvitationAcceptController`)
- **Signed URLs**: Secure invitation acceptance links
- **Automatic Membership**: User becomes member on acceptance
- **Current Organization**: Sets organization as user's current
- **Security**: Only valid, non-expired invitations can be accepted

### Test Cases
- Allows users to accept invitations and join organizations
- Properly sets current organization after acceptance
- Removes invitation after successful acceptance
- Redirects to dashboard after joining

## Data Models

### Organization
- `name`: Organization name (required)
- `user_id`: Owner reference
- `personal`: Boolean flag for personal organizations
- Subscription relationship for billing

### Organization Membership
- Many-to-many relationship between users and organizations
- Role-based access (owner vs member)
- Current organization tracking on user model

### Organization Invitations
- Email-based invitations
- Organization relationship
- Unique email constraint per organization
- Expiration handling

## Security Considerations

### Authorization
- Ownership-based access control
- Member vs owner permission distinction
- Proper authorization checks on all operations

### Data Isolation
- Users can only access their own organizations
- Member management restricted to owners
- Invitation system maintains organization boundaries

### Current Organization Management
- Automatic updates when membership changes
- Prevents orphaned current organization references
- Proper fallback handling when organization is removed