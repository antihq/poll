# Email Integration & HTML Generation Specification

## Overview
This specification covers the technical implementation of embedding Antipoll polls into emails, including HTML generation, cross-platform compatibility, and response collection mechanisms.

## HTML Poll Generation

### Core Requirements
- **Universal Compatibility**: Work across all email clients
- **Minimal Dependencies**: No external JavaScript libraries
- **Responsive Design**: Function on mobile and desktop email clients
- **Fallback Support**: Graceful degradation for unsupported features

### HTML Structure
```html
<!-- Platform-agnostic poll HTML -->
<div class="antipoll-poll" data-poll-id="123">
  <div class="antipoll-question">What's your favorite feature?</div>
  <div class="antipoll-options">
    <a href="https://antipoll.co/r/123/1/abc123" class="antipoll-option">
      Analytics
    </a>
    <a href="https://antipoll.co/r/123/2/def456" class="antipoll-option">
      Design
    </a>
    <a href="https://antipoll.co/r/123/3/ghi789" class="antipoll-option">
      Performance
    </a>
  </div>
</div>
```

### Layout Options

#### Vertical Layout (Default)
```html
<div class="antipoll-options-vertical">
  <div class="antipoll-option">
    <a href="{response_url}" class="antipoll-button">
      <span class="antipoll-emoji">{emoji}</span>
      <span class="antipoll-text">{answer}</span>
    </a>
  </div>
  <!-- More options -->
</div>
```

#### Horizontal Layout
```html
<div class="antipoll-options-horizontal">
  <div class="antipoll-option">
    <a href="{response_url}" class="antipoll-button">
      <span class="antipoll-emoji">{emoji}</span>
      <span class="antipoll-text">{answer}</span>
    </a>
  </div>
  <!-- More options -->
</div>
```

## Response Collection Mechanisms

### Primary Method: Redirect-Based Tracking
- **Click Tracking**: Each answer option links to a unique tracking URL
- **Response Recording**: Server records response before redirecting
- **Redirect Flow**: User clicks → Antipoll records → Redirects to final URL
- **Advantages**: Works in all email clients, no JavaScript required

### Tracking URL Structure
```
https://antipoll.co/r/{poll_id}/{option_id}/{tracking_hash}
```

### Response Flow
1. User clicks poll option in email
2. Email client opens tracking URL in browser
3. Antipoll server records response
4. Server redirects to configured destination:
   - Custom redirect URL (if set)
   - Thank you page (if configured)
   - Default Antipoll results page

### Email Address Collection

#### Integrated Platforms (Mailchimp, Kit, Ghost, etc.)
- **Automatic Detection**: Platform provides subscriber email
- **Contact Linking**: Response automatically linked to contact
- **No Additional Steps**: Seamless user experience

#### Generic Platforms (Gmail, Apple Mail)
- **Optional Email Collection**: Poll can require email input
- **Email Collection Form**: Simple form after initial click
- **Privacy Compliance**: Clear disclosure of data collection

## CSS Styling & Branding

### Inline CSS Requirements
- **Email Client Compatibility**: All styles must be inline
- **Minimal Dependencies**: No external stylesheets
- **Responsive Design**: Mobile-first approach

### Base Styles
```css
.antipoll-poll {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  max-width: 600px;
  margin: 20px 0;
}

.antipoll-question {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
  color: #333;
}

.antipoll-button {
  display: inline-block;
  padding: 12px 20px;
  margin: 4px;
  background: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 500;
}

.antipoll-button:hover {
  background: #0056b3;
}
```

### Layout-Specific Styles
```css
/* Vertical Layout */
.antipoll-options-vertical .antipoll-option {
  display: block;
  margin: 8px 0;
}

/* Horizontal Layout */
.antipoll-options-horizontal {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.antipoll-options-horizontal .antipoll-option {
  flex: 1;
  min-width: 120px;
}
```

### Branding Options
- **Default Branding**: "Powered by Antipoll" with link
- **White-Label**: No branding displayed
- **Custom Branding**: Optional custom footer message

## Platform-Specific Optimizations

### Mailchimp Integration
- **Merge Tags**: Use `*|EMAIL|*` for subscriber identification
- **Template Compatibility**: Tested with Mailchimp template editor
- **Reporting Integration**: Response data available in Mailchimp reports

### Kit (ConvertKit) Integration
- **Subscriber Linking**: Automatic email address capture
- **Custom Fields**: Store poll responses in subscriber custom fields
- **Automation Triggers**: Poll responses can trigger automations

### Ghost Integration
- **Member Integration**: Link responses to Ghost members
- **Newsletter Compatibility**: Optimized for Ghost newsletter emails
- **Analytics Integration**: Response data in Ghost analytics

### Gmail & Generic Email
- **Standalone Operation**: No platform dependencies
- **Email Collection**: Optional email requirement
- **Universal Compatibility**: Works in any email client

## Advanced Features Implementation

### Conditional Redirects
```html
<a href="https://antipoll.co/r/{poll_id}/{option_id}/{hash}?redirect={encoded_url}">
```

### Feedback Collection
- **Two-Step Process**: Initial click → Feedback form → Submit
- **Minimal Form**: Single textarea for feedback
- **Optional vs Required**: Configurable per poll

### Auto-Submit vs Manual Submit
- **Auto-Submit**: Direct link records response immediately
- **Manual Submit**: JavaScript-enhanced interface for supported clients
- **Graceful Fallback**: Always works without JavaScript

## Technical Implementation

### Backend Response Handler
```php
// Response tracking endpoint
Route::get('/r/{pollId}/{optionId}/{hash}', [PollResponseController::class, 'track']);

public function track($pollId, $optionId, $hash)
{
    // Validate hash
    // Record response
    // Get redirect URL
    // Redirect to destination
}
```

### HTML Generation Service
```php
class PollHtmlGenerator
{
    public function generate(Poll $poll): string
    {
        // Generate HTML structure
        // Apply inline styles
        // Add tracking URLs
        // Include branding
        // Return complete HTML
    }
}
```

### Email Platform Detection
```php
class PlatformDetector
{
    public function detectFromRequest(Request $request): ?string
    {
        // Detect platform from user agent, referrer, or headers
        // Return platform identifier for integration
    }
}
```

## Security & Privacy

### Tracking Security
- **Hash Validation**: Prevent URL manipulation
- **Rate Limiting**: Prevent response spamming
- **Data Privacy**: GDPR-compliant data handling

### Email Privacy
- **Consent Collection**: Clear disclosure for email collection
- **Data Minimization**: Only collect necessary information
- **Right to Deletion**: User can request data removal

## Testing & Quality Assurance

### Email Client Testing
- **Major Clients**: Gmail, Outlook, Apple Mail, Yahoo
- **Mobile Clients**: iOS Mail, Gmail Android, Outlook Mobile
- **Desktop Clients**: Thunderbird, Windows Mail

### Platform Integration Testing
- **API Testing**: Verify platform integrations work correctly
- **Template Testing**: Test HTML in platform template editors
- **Response Tracking**: Verify response collection accuracy

### Performance Testing
- **Load Testing**: Handle high-volume response submission
- **Render Testing**: Ensure fast email rendering
- **Redirect Performance**: Minimize redirect latency