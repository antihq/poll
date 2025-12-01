# Antipoll System Specification

## Overview
Antipoll is a poll and survey creation platform designed specifically for email integration. Users can create interactive polls, embed them in any email platform, and collect responses through a centralized dashboard.

## Core Features

### Poll Creation
- Create polls with up to 10 answer options
- Support for text and emoji answers (or both)
- Drag-and-drop reordering of answers
- Vertical list or horizontal layout options
- Edit polls even after responses are collected
- Remove answers while preserving historical response data

### Advanced Poll Options
- **Redirect URLs**: Send respondents to different web pages based on their answer selection
- **Free-form feedback**: Collect additional text feedback for specific answers or all responses
- **Email requirement**: Force email collection for platforms like Gmail
- **Auto-submit**: Record responses immediately on click or show selection with submit button
- **Response control**: Stop receiving responses at any time
- **Custom thank you messages**: Markdown-supported customizable completion messages
- **White-label option**: Hide Antipoll branding for professional appearance

### Response Management
- **Real-time analytics**: Bar charts visualizing response distribution
- **Response metrics**: View counts and percentages for each answer
- **Respondent tracking**: Display all responses with email addresses and timestamps
- **Feedback overview**: Dedicated view for all free-form responses
- **Data export**: Export response data for analysis

### Email Platform Integration
- **Universal compatibility**: Works with Gmail, Apple Mail, Mailchimp, Kit, Ghost, Beehiiv, Loops, HubSpot, Sendy, and more
- **Copy-paste implementation**: Simple HTML copy-paste into any email editor
- **Contact linking**: Automatically link responses to subscriber/contact data where supported
- **Platform-specific optimizations**: Tailored experiences for major email platforms

## User Experience Flow

### 1. User Onboarding with Cashier
1. User signs up for 14-day free trial
2. Cashier automatically sets up trial period
3. Full access to all features during trial period
4. Onboarded with tutorial and guided poll creation
5. Cashier handles trial expiration and conversion

### 2. Poll Creation
1. User navigates to `/polls/create` page component (`pages::polls.create`)
2. Enters poll question and up to 10 answer options using Flux UI forms
3. Configures advanced options (redirects, feedback, etc.) in organized sections
4. Chooses layout and styling preferences with Flux UI components
5. Generates HTML poll code with Livewire form submission

### 2. Email Integration
1. User copies generated HTML code
2. Pastes code into their email platform
3. Poll appears as interactive elements in email
4. Recipients can click options directly in email

### 3. Response Collection
1. Recipients click poll options in email
2. Response is recorded in Antipoll dashboard
3. Optional: Redirect to specified URL
4. Optional: Show thank you message or feedback form

### 4. Analytics & Management
1. User navigates to `/polls/{poll}/analytics` page component (`pages::polls.analytics`)
2. Views real-time response data with Flux UI charts
3. Analyzes response patterns and metrics using interactive filters
4. Exports data for further analysis from dedicated export page
5. Manages poll settings (close polls, edit options) from management page components
6. Organization-based analytics for shared polls
7. Role-based access to analytics within organizations
6. Organization-based analytics for shared polls
7. Role-based access to analytics within organizations
6. Organization-based analytics for shared polls
7. Role-based access to analytics within organizations
8. Organization subscription billing and management
7. Role-based access to analytics within organizations
6. Organization-based analytics for shared polls

## Technical Architecture

### Frontend Requirements
- Livewire 4 page components for full-page functionality
- Flux UI components for consistent design system
- Drag-and-drop interface for answer ordering
- Real-time chart visualization (bar charts)
- Flux UI WYSIWYG editor for thank you messages
- Copy-to-clipboard functionality for HTML code
- Mobile-responsive design with Flux UI responsive utilities

### Backend Requirements
- Livewire 4 page component-based architecture
- Response tracking and storage
- Email platform integration endpoints
- Analytics and reporting engine
- User authentication and authorization
- Laravel Cashier subscription management and Stripe billing

### Page Component Structure
```
resources/views/pages/
├── ⚡dashboard.blade.php
├── polls/
│   ├── ⚡create.blade.php
│   ├── ⚡edit.blade.php
│   ├── ⚡index.blade.php
│   ├── ⚡show.blade.php
│   ├── ⚡analytics.blade.php
│   ├── ⚡integrate.blade.php
│   └── ⚡embed.blade.php
├── billing/
│   ├── ⚡dashboard.blade.php
│   ├── ⚡purchase.blade.php
│   └── ⚡history.blade.php
└── settings/
    ├── ⚡profile.blade.php
    └── ⚡appearance.blade.php
```

### Component Format
Each `.blade.php` file contains both PHP class logic and HTML template in a single file using Livewire 4's single-file component format.

### Database Schema
- Users table (authentication, subscription status)
- Organizations table (organization billing and management)
- Organization Users table (many-to-many relationship)
- Subscriptions table (Stripe subscription data)
- Polls table (questions, settings, metadata, organization_id)
- Poll Options table (answer choices, ordering)
- Responses table (individual responses, timestamps)
- Feedback table (free-form text responses)
- Integrations table (platform-specific data)
- Usage Metrics table (subscription limits tracking)

### Email Integration
- HTML generation for cross-platform compatibility
- Tracking pixel or redirect-based response collection
- Platform-specific contact linking
- Fallback mechanisms for unsupported platforms

## Business Model

### Pricing Structure
- **14-day Free Trial**: Full access to all features
- **Starter Plan**: $29/month - Up to 10 active polls
- **Professional Plan**: $79/month - Up to 50 active polls
- **Business Plan**: $199/month - Unlimited active polls
- **Enterprise Plan**: Custom pricing - Advanced features & support

### Revenue Streams
- Monthly recurring subscription revenue
- Annual subscription discounts (20% off)
- Enterprise custom contracts
- Potential add-on features (white-label, advanced analytics)

## Success Metrics
- Poll creation rate
- Response collection rates
- User conversion from free to paid
- Platform integration usage
- User retention and repeat usage

## Security & Privacy
- GDPR compliance for email data handling
- Secure response data storage
- User data privacy controls
- Safe HTML generation for email compatibility
- Rate limiting and abuse prevention