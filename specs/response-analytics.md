# Response Analytics & Dashboard Specification

## Overview
This specification covers the analytics dashboard, response visualization, and data management features for tracking and analyzing poll responses in Antipoll.

## Dashboard Overview

### Main Dashboard Layout
- **Poll Summary Cards**: Overview of all user polls with key metrics
- **Quick Actions**: Create new poll, view recent activity
- **Response Analytics**: Real-time response tracking and visualization
- **Navigation**: Easy access to detailed poll management

### Poll Summary Cards
- **Poll Title**: Question text with truncation for long questions
- **Response Count**: Total number of responses received
- **Status Indicator**: Active/Closed/Draft status
- **Last Activity**: Timestamp of most recent response
- **Quick Actions**: View results, edit poll, share, duplicate

## Detailed Poll Analytics

### Response Visualization
- **Bar Charts**: Visual representation of answer distribution
- **Percentage Display**: Show relative popularity of each option
- **Response Counts**: Absolute numbers for each answer
- **Real-time Updates**: Live updates as new responses arrive

### Chart Implementation with Livewire 4 + Flux UI
```php
// Livewire component for chart rendering
class PollAnalytics extends Component
{
    public Poll $poll;
    public $chartData;
    
    public function mount(Poll $poll)
    {
        $this->poll = $poll;
        $this->refreshChartData();
    }
    
    public function refreshChartData()
    {
        $this->chartData = $this->poll->responses()
            ->selectRaw('poll_option_id, COUNT(*) as count')
            ->groupBy('poll_option_id')
            ->with('pollOption')
            ->get()
            ->map(function ($response) {
                return [
                    'label' => $response->pollOption->answer_text,
                    'count' => $response->count,
                    'percentage' => round(($response->count / $this->poll->responses->count()) * 100, 1)
                ];
            });
    }
}
```

### Flux UI Chart Components
```blade
<!-- Using Flux UI for chart visualization -->
<flux:chart type="bar" :data="$chartData">
    <flux:chart.x-axis data-key="label" />
    <flux:chart.y-axis data-key="count" />
    <flux:chart.series name="Responses" data-key="count" />
</flux:chart>

<!-- Response metrics with Flux UI cards -->
<flux:grid>
    <flux:col>
        <flux:card>
            <flux:card.header>Total Responses</flux:card.header>
            <flux:card.content>{{ $poll->responses->count() }}</flux:card.content>
        </flux:card>
    </flux:col>
    <flux:col>
        <flux:card>
            <flux:card.header>Response Rate</flux:card.header>
            <flux:card.content>{{ $poll->response_rate }}%</flux:card.content>
        </flux:card>
    </flux:col>
</flux:grid>
```

### Response Metrics Panel
- **Total Responses**: Overall response count
- **Unique Respondents**: Deduplicated user count (when available)
- **Response Rate**: Percentage of views that resulted in responses
- **Average Response Time**: Time between poll creation and first response
- **Peak Response Times**: Heatmap of response activity by time/day

## Individual Response Management

### Response List View
- **Table Format**: Detailed list of all responses
- **Columns**: Response ID, Answer Selected, Email Address, Timestamp, IP Address
- **Sorting**: Sortable by any column
- **Filtering**: Filter by answer, date range, email domain
- **Search**: Free-text search across response data

### Response Details with Flux UI
```blade
<!-- Flux UI table for response data -->
<flux:table>
    <flux:table.header>
        <flux:table.row>
            <flux:table.cell>Response ID</flux:table.cell>
            <flux:table.cell>Answer</flux:table.cell>
            <flux:table.cell>Email</flux:table.cell>
            <flux:table.cell>Time</flux:table.cell>
            <flux:table.cell>Platform</flux:table.cell>
            <flux:table.cell>Actions</flux:table.cell>
        </flux:table.row>
    </flux:table.header>
    <flux:table.body>
        @foreach($poll->responses as $response)
            <flux:table.row>
                <flux:table.cell>{{ $response->id }}</flux:table.cell>
                <flux:table.cell>{{ $response->pollOption->answer_text }}</flux:table.cell>
                <flux:table.cell>{{ $response->email }}</flux:table.cell>
                <flux:table.cell>{{ $response->created_at->diffForHumans() }}</flux:table.cell>
                <flux:table.cell>{{ $response->platform }}</flux:table.cell>
                <flux:table.cell>
                    <flux:dropdown>
                        <flux:dropdown.trigger>Actions</flux:dropdown.trigger>
                        <flux:dropdown.menu>
                            <flux:dropdown.item wire:click="viewResponse({{ $response->id }})">View</flux:dropdown.item>
                            <flux:dropdown.item wire:click="deleteResponse({{ $response->id }})">Delete</flux:dropdown.item>
                        </flux:dropdown.menu>
                    </flux:dropdown>
                </flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.body>
</flux:table>
```

### Individual Response Actions
- **View Details**: Full response information including metadata
- **Edit Response**: Modify response data (admin only)
- **Delete Response**: Remove individual response
- **Export Response**: Download single response data

## Feedback Management

### Free-Form Feedback Overview
- **Feedback Summary**: Count of responses with feedback
- **Feedback List**: All collected feedback in chronological order
- **Feedback Analytics**: Word frequency, sentiment analysis (optional)
- **Export Options**: Download feedback as CSV/JSON

### Feedback Display with Flux UI
```blade
<!-- Flux UI feedback display -->
<flux:card>
    <flux:card.header>
        <h3>Collected Feedback ({{ $poll->feedback->count() }} responses)</h3>
    </flux:card.header>
    <flux:card.content>
        <div class="space-y-4">
            @foreach($poll->feedback as $feedback)
                <flux:callout>
                    <flux:callout.content>
                        <div class="font-medium">Selected: "{{ $feedback->response->pollOption->answer_text }}"</div>
                        <div class="text-gray-600 mt-1">{{ $feedback->content }}</div>
                        <div class="text-sm text-gray-500 mt-2">
                            {{ $feedback->response->email }} - {{ $feedback->created_at->diffForHumans() }}
                        </div>
                    </flux:callout.content>
                </flux:callout>
            @endforeach
        </div>
    </flux:card.content>
</flux:card>
```

## Data Export & Reporting

### Export Options
- **CSV Export**: Spreadsheet-compatible format
- **JSON Export**: Machine-readable format for API integration
- **PDF Report**: Formatted report with charts and summaries
- **Excel Export**: Advanced formatting with pivot tables

### Export Data Structure
```csv
Response ID,Poll Question,Answer Selected,Email Address,Timestamp,Platform,Feedback
1,"What's your favorite feature?","Analytics","user@example.com","2025-01-15 10:30:00","Mailchimp","Love the real-time updates!"
```

### Automated Reports
- **Daily/Weekly Summaries**: Email reports of poll activity
- **Scheduled Exports**: Automatic data delivery to specified email
- **API Access**: Programmatic access to response data

## Real-Time Features

### Live Response Updates with Livewire 4
- **Livewire Wire Poll**: Real-time response updates without WebSockets
- **Live Charts**: Animated chart updates as responses arrive using Livewire
- **Flux UI Notifications**: Browser notifications for new responses
- **Activity Feed**: Real-time feed of response activity with Livewire polling

### Response Notifications
- **Email Alerts**: Optional email notifications for new responses
- **Threshold Alerts**: Notifications when response milestones reached
- **Daily Digest**: Summary of daily response activity

## Advanced Analytics

### Response Patterns
- **Time Analysis**: Response patterns by hour/day/week
- **Geographic Distribution**: Location-based response analysis (when available)
- **Platform Performance**: Response rates by email platform
- **Answer Correlation**: Cross-poll answer pattern analysis

### Comparative Analysis
- **Poll Comparison**: Side-by-side comparison of multiple polls
- **Trend Analysis**: Response trends over time
- **Audience Insights**: Demographic analysis (when available)

## User Interface Components

### Dashboard Navigation
- **Main Dashboard**: Overview of all polls
- **Poll Details**: Individual poll analytics
- **Response Management**: Detailed response data
- **Export Center**: Data export and reporting tools

### Filtering & Search with Flux UI
- **Flux UI Date Range Picker**: Filter responses by time period
- **Flux UI Select**: Filter responses for specific answers
- **Flux UI Multi-select**: Filter by email platform
- **Flux UI Search**: Search feedback and response data

### Visualization Controls
- **Chart Type Selection**: Bar, pie, line chart options
- **Color Themes**: Customizable chart colors
- **Data Granularity**: Daily, weekly, monthly aggregation
- **Export Charts**: Download charts as images

## Technical Implementation

### Livewire 4 Page Components Architecture
```php
// Main Dashboard Page Component
class Dashboard extends Component
{
    public $polls;
    public $selectedPoll = null;
    public $responses;
    public $analytics;
    public $filters = [
        'dateRange' => null,
        'answer' => null,
        'platform' => null,
        'search' => ''
    ];
    
    protected $listeners = ['refreshAnalytics' => '$refresh'];
    
    public function mount()
    {
        $this->loadPolls();
    }
    
    public function selectPoll($pollId)
    {
        $this->selectedPoll = Poll::find($pollId);
        $this->loadAnalytics();
    }
    
    public function updatedFilters()
    {
        $this->loadAnalytics();
    }
    
    public function render()
    {
        return view('livewire.pages.dashboard')
            ->layout('layouts.app');
    }
}
```

### Livewire 4 Page Components
```php
// Page components for analytics
php artisan make:livewire pages::dashboard
php artisan make:livewire pages::polls.analytics
php artisan make:livewire pages::polls.responses
php artisan make:livewire pages::polls.feedback
php artisan make:livewire pages::polls.export
```

### Analytics Page Component Structure
```php
<?php

use App\Models\Poll;
use Livewire\Component;

new class extends Component {
    public Poll $poll;
    public $chartData;
    public $responses;
    public $filters = [];
    
    public function mount(Poll $poll)
    {
        $this->poll = $poll;
        $this->refreshData();
    }
    
    public function refreshData()
    {
        $this->chartData = $this->poll->getChartData();
        $this->responses = $this->poll->getFilteredResponses($this->filters);
    }
    
    public function exportData($format)
    {
        return $this->poll->exportResponses($format);
    }
};
?>
<!-- Analytics dashboard HTML here -->
```

### Route Definitions for Analytics Pages
```php
// routes/web.php
Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
Route::livewire('/polls/{poll}/analytics', 'pages::polls.analytics')->name('polls.analytics');
Route::livewire('/polls/{poll}/responses', 'pages::polls.responses')->name('polls.responses');
Route::livewire('/polls/{poll}/feedback', 'pages::polls.feedback')->name('polls.feedback');
Route::livewire('/polls/{poll}/export', 'pages::polls.export')->name('polls.export');
```

### Database Optimization
- **Indexed Queries**: Optimized response retrieval
- **Caching Strategy**: Redis caching for frequently accessed data
- **Pagination**: Efficient handling of large response sets
- **Aggregation Queries**: Pre-computed analytics for performance

## Performance Considerations

### Chart Performance
- **Lazy Loading**: Load charts on demand
- **Data Sampling**: Sample large datasets for visualization
- **Caching**: Cache chart data and rendered charts
- **Progressive Loading**: Load data in chunks for large polls

### Response Table Performance
- **Virtual Scrolling**: Handle large response lists efficiently
- **Server-Side Processing**: Filter and sort on server
- **Column Resizing**: User-customizable table layouts
- **Export Optimization**: Efficient data export for large datasets

## Mobile Responsiveness

### Mobile Dashboard
- **Touch-Friendly**: Optimized for touch interactions
- **Responsive Charts**: Charts adapt to screen size
- **Simplified Navigation**: Collapsible menu for mobile
- **Swipe Gestures**: Navigate between polls with gestures

### Mobile-Specific Features
- **Push Notifications**: Mobile app notifications for responses
- **Offline Mode**: Cached data for offline viewing
- **Touch Gestures**: Swipe to delete, long press for options
- **Responsive Tables**: Horizontal scroll for data tables