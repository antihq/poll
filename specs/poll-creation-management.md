# Poll Creation & Management Specification

## Overview
This specification covers the poll creation interface, management features, and the core functionality for building and configuring polls within the Antipoll system.

## Poll Creation Interface

### Basic Poll Structure
- **Question Field**: Text input for poll question (required)
- **Answer Options**: Up to 10 configurable answer choices
  - Text input for each answer
  - Emoji support alongside text
  - Drag-and-drop reordering capability
  - Add/remove answers dynamically
- **Layout Selection**: 
  - Vertical list (default)
  - Horizontal row layout

### Advanced Configuration Options

#### Response Handling
- **Auto-submit vs Manual Submit**
  - Auto-submit: Record response immediately on click
  - Manual submit: Show selection with submit button
- **Email Collection**
  - Optional requirement for email address
  - Essential for platforms like Gmail
  - Fallback for platforms without contact integration

#### Answer-Specific Features
- **Redirect URLs**
  - Individual redirect URL per answer option
  - Opens in new tab/window
  - Optional field for each answer
- **Conditional Feedback**
  - Enable free-form feedback for specific answers
  - Text area appears after answer selection
  - Configurable per answer option

#### Global Poll Settings
- **Free-form Feedback Collection**
  - Enable feedback collection for all responses
  - Text area prompt customization
  - Optional vs required feedback
- **Custom Thank You Message**
  - Markdown-supported content
  - Customizable styling and links
  - Default message if not specified
- **Response Control**
  - Open/close poll for new responses
  - Preserve existing responses when closed
- **Branding Options**
  - Hide/show Antipoll branding
  - White-label experience option

## Poll Management Features

### Poll Dashboard
- **Poll List View**
  - All user polls with status indicators
  - Response counts and last activity
  - Quick actions (edit, duplicate, close, delete)
  - Search and filter capabilities
- **Poll Status Indicators**
  - Active (accepting responses)
  - Closed (no longer accepting responses)
  - Draft (not yet published)

### Poll Editing
- **Live Editing**
  - Edit question and answers after responses collected
  - Preserve all historical response data
  - Version tracking for major changes
- **Answer Management**
  - Add new answers at any time
  - Remove answers (data preserved)
  - Reorder answers with drag-and-drop
  - Edit answer text/emoji

### Poll Duplication
- **Clone Functionality**
  - Copy entire poll configuration
  - Reset response data
  - Maintain answer structure and settings
  - Quick creation of similar polls

## Technical Implementation

### Frontend Components
- **Livewire 4 Components**
  - Poll Builder Livewire component with drag-and-drop
  - Real-time preview of poll appearance
  - Flux UI forms for validation and error handling
  - Auto-save functionality with Livewire wire:model
- **Flux UI Integration**
  - Dynamic add/remove answer components using Flux UI
  - Drag-and-drop reordering system with Livewire
  - Flux UI emoji picker integration
  - URL validation for redirects using Flux UI inputs

### Livewire 4 Page Components
```
php artisan make:livewire pages::polls.create
php artisan make:livewire pages::polls.edit
php artisan make:livewire pages::polls.index
php artisan make:livewire pages::polls.show
php artisan make:livewire pages::polls.analytics
```

### Livewire Page Component Structure
```php
<?php

use App\Models\Poll;
use Livewire\Component;

new class extends Component {
    public $question;
    public $answers = [];
    public $layout = 'vertical';
    public $autoSubmit = true;
    public $requireEmail = false;
    public $thankYouMessage;
    
    public function createPoll()
    {
        // Create poll logic
    }
    
    public function addAnswer()
    {
        // Add answer logic
    }
    
    public function removeAnswer($index)
    {
        // Remove answer logic
    }
    
    public function mount()
    {
        // Initialize page data
        $this->answers = [
            ['text' => '', 'emoji' => ''],
            ['text' => '', 'emoji' => ''],
        ];
    }
};
?>
<!-- Poll creation form HTML here -->
```

### Route Definitions
```php
// routes/web.php
Route::livewire('/polls/create', 'pages::polls.create')->name('polls.create');
Route::livewire('/polls/{poll}/edit', 'pages::polls.edit')->name('polls.edit');
Route::livewire('/polls', 'pages::polls.index')->name('polls.index');
Route::livewire('/polls/{poll}', 'pages::polls.show')->name('polls.show');
Route::livewire('/polls/{poll}/analytics', 'pages::polls.analytics')->name('polls.analytics');
```

### Database Schema
```sql
polls:
- id, organization_id, question
- layout_type, auto_submit
- require_email, collect_feedback
- thank_you_message, hide_branding
- status, created_at, updated_at

poll_options:
- id, poll_id, answer_text
- answer_emoji, redirect_url
- collect_feedback, sort_order
- created_at, updated_at
```

## User Experience Flow

### Creation Process with Page Components
1. User navigates to `/polls/create` (pages::polls.create page component)
2. Poll is created for the current organization context
3. Enters poll question using Flux UI inputs
4. Adds answer options (text/emoji) with dynamic form fields
5. Configures layout preference using Flux UI radio buttons
6. Sets up advanced options (redirects, feedback) in collapsible sections
7. Customizes thank you message using Flux UI textarea
8. Chooses branding options using Flux UI toggle
9. Saves and generates HTML code with Livewire form submission

### Editing Process with Page Components
1. User navigates to `/polls/{poll}/edit` (pages::polls.edit page component)
2. Page component mounts with poll data using route model binding
3. Makes desired changes to question/answers using Flux UI forms
4. Updates configuration options in organized sections
5. Saves changes with Livewire form submission
6. System updates HTML code if needed
7. Historical response data preserved
8. Redirects to poll show page with success message

### Validation Rules
- Question required (max 255 characters)
- Minimum 2 answer options required
- Maximum 10 answer options allowed
- Answer text required if no emoji
- Redirect URLs must be valid URLs
- Thank you message max 1000 characters

## Error Handling
- Real-time validation feedback
- Graceful degradation for unsupported features
- Clear error messages for invalid inputs
- Auto-save recovery for interrupted sessions

## Performance Considerations
- Efficient drag-and-drop rendering
- Optimized emoji picker
- Lazy loading for large poll lists
- Caching for frequently accessed polls
- Minimal API calls during editing