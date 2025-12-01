# Livewire 4 Page Components Architecture

## Overview
This specification covers the correct implementation of Antipoll using Livewire 4 view-based page components according to the official documentation.

## Component Structure

### Single-File Components
Livewire 4 uses single-file components by default, where PHP class logic and HTML template are combined in one `.blade.php` file.

### File Organization
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
Each component follows this structure:
```php
<?php

use Livewire\Component;

new class extends Component {
    // Properties
    public $title = '';
    
    // Methods
    public function save()
    {
        // Logic here
    }
    
    // Lifecycle hooks
    public function mount()
    {
        // Initialize component
    }
};
?>
<!-- HTML template -->
<div>
    <input wire:model="title" type="text">
    <button wire:click="save">Save</button>
</div>
```

## Page Component Creation

### Creating Page Components
```bash
# Dashboard
php artisan make:livewire pages::dashboard

# Poll Management
php artisan make:livewire pages::polls.create
php artisan make:livewire pages::polls.edit
php artisan make:livewire pages::polls.index
php artisan make:livewire pages::polls.show
php artisan make:livewire pages::polls.analytics
php artisan make:livewire pages::polls.integrate

# Billing
php artisan make:livewire pages::billing.dashboard
php artisan make:livewire pages::billing.purchase
php artisan make:livewire pages::billing.history

# Settings
php artisan make:livewire pages::settings.profile
php artisan make:livewire pages::settings.appearance
```

This creates single-file components at:
- `resources/views/pages/⚡dashboard.blade.php`
- `resources/views/pages/polls/⚡create.blade.php`
- etc.

## Route Configuration

### Web Routes
```php
// routes/web.php
use Illuminate\Support\Facades\Route;

// Dashboard
Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

// Polls
Route::livewire('/polls', 'pages::polls.index')->name('polls.index');
Route::livewire('/polls/create', 'pages::polls.create')->name('polls.create');
Route::livewire('/polls/{poll}', 'pages::polls.show')->name('polls.show');
Route::livewire('/polls/{poll}/edit', 'pages::polls.edit')->name('polls.edit');
Route::livewire('/polls/{poll}/analytics', 'pages::polls.analytics')->name('polls.analytics');
Route::livewire('/polls/{poll}/integrate', 'pages::polls.integrate')->name('polls.integrate');
Route::livewire('/polls/{poll}/embed', 'pages::polls.embed')->name('polls.embed');

// Billing
Route::livewire('/billing', 'pages::billing.dashboard')->name('billing.dashboard');
Route::livewire('/billing/purchase', 'pages::billing.purchase')->name('billing.purchase');
Route::livewire('/billing/history', 'pages::billing.history')->name('billing.history');

// Settings
Route::livewire('/settings/profile', 'pages::settings.profile')->name('settings.profile');
Route::livewire('/settings/appearance', 'pages::settings.appearance')->name('settings.appearance');
```

## Component Examples

### Dashboard Page Component
```php
<?php

use App\Models\Poll;
use Livewire\Component;

new class extends Component {
    public $recentPolls;
    public $totalResponses;
    public $activePolls;
    public $creditsRemaining;

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->recentPolls = auth()->user()->polls()
            ->withCount('responses')
            ->latest()
            ->take(5)
            ->get();

        $this->totalResponses = auth()->user()->polls()
            ->join('poll_responses', 'polls.id', '=', 'poll_responses.poll_id')
            ->count();

        $this->activePolls = auth()->user()->polls()
            ->where('status', 'active')
            ->count();

        $this->creditsRemaining = auth()->user()->availableCredits();
    }
};
?>
<div class="space-y-6">
    <!-- Welcome Section -->
    <flux:header>
        <flux:heading>Welcome back, {{ auth()->user()->name }}!</flux:heading>
        <flux:text>Here's what's happening with your polls today.</flux:text>
    </flux:header>

    <!-- Stats Grid -->
    <flux:grid class="grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <flux:col>
            <flux:card>
                <flux:card.header>
                    <flux:card.title>Total Polls</flux:card.title>
                </flux:card.header>
                <flux:card.content>
                    <div class="text-3xl font-bold">{{ auth()->user()->polls()->count() }}</div>
                </flux:card.content>
            </flux:card>
        </flux:col>

        <flux:col>
            <flux:card>
                <flux:card.header>
                    <flux:card.title>Total Responses</flux:card.title>
                </flux:card.header>
                <flux:card.content>
                    <div class="text-3xl font-bold">{{ $totalResponses }}</div>
                </flux:card.content>
            </flux:card>
        </flux:col>

        <flux:col>
            <flux:card>
                <flux:card.header>
                    <flux:card.title>Active Polls</flux:card.title>
                </flux:card.header>
                <flux:card.content>
                    <div class="text-3xl font-bold">{{ $activePolls }}</div>
                </flux:card.content>
            </flux:card>
        </flux:col>

        <flux:col>
            <flux:card>
                <flux:card.header>
                    <flux:card.title>Credits Remaining</flux:card.title>
                </flux:card.header>
                <flux:card.content>
                    <div class="text-3xl font-bold">{{ $creditsRemaining }}</div>
                </flux:card.content>
            </flux:card>
        </flux:col>
    </flux:grid>

    <!-- Recent Polls -->
    <flux:card>
        <flux:card.header>
            <flux:card.title>Recent Polls</flux:card.title>
            <flux:button href="{{ route('polls.create') }}">Create New Poll</flux:button>
        </flux:card.header>
        <flux:card.content>
            @if($recentPolls->count() > 0)
                <flux:table>
                    <flux:table.header>
                        <flux:table.row>
                            <flux:table.cell>Question</flux:table.cell>
                            <flux:table.cell>Responses</flux:table.cell>
                            <flux:table.cell>Status</flux:table.cell>
                            <flux:table.cell>Created</flux:table.cell>
                            <flux:table.cell>Actions</flux:table.cell>
                        </flux:table.row>
                    </flux:table.header>
                    <flux:table.body>
                        @foreach($recentPolls as $poll)
                            <flux:table.row>
                                <flux:table.cell>{{ Str::limit($poll->question, 50) }}</flux:table.cell>
                                <flux:table.cell>{{ $poll->responses_count }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge variant="{{ $poll->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ $poll->status }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $poll->created_at->diffForHumans() }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:dropdown>
                                        <flux:dropdown.trigger>Actions</flux:dropdown.trigger>
                                        <flux:dropdown.menu>
                                            <flux:dropdown.item href="{{ route('polls.show', $poll) }}">View</flux:dropdown.item>
                                            <flux:dropdown.item href="{{ route('polls.analytics', $poll) }}">Analytics</flux:dropdown.item>
                                            <flux:dropdown.item href="{{ route('polls.edit', $poll) }}">Edit</flux:dropdown.item>
                                        </flux:dropdown.menu>
                                    </flux:dropdown>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.body>
                </flux:table>
            @else
                <flux:callout>
                    <flux:callout.content>
                        You haven't created any polls yet. 
                        <flux:button href="{{ route('polls.create') }}" variant="ghost">Create your first poll</flux:button>
                    </flux:callout.content>
                </flux:callout>
            @endif
        </flux:card.content>
    </flux:card>
</div>
```

### Poll Creation Page Component
```php
<?php

use App\Models\Poll;
use App\Models\PollOption;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $question;
    public $answers = [];
    public $layout = 'vertical';
    public $autoSubmit = true;
    public $requireEmail = false;
    public $collectFeedback = false;
    public $thankYouMessage;
    public $hideBranding = false;

    protected $rules = [
        'question' => 'required|max:255',
        'answers.*.text' => 'required_without:answers.*.emoji',
        'layout' => 'required|in:vertical,horizontal',
        'thankYouMessage' => 'nullable|max:1000',
    ];

    public function mount()
    {
        $this->answers = [
            ['text' => '', 'emoji' => '', 'redirect_url' => ''],
            ['text' => '', 'emoji' => '', 'redirect_url' => ''],
        ];
    }

    public function addAnswer()
    {
        if (count($this->answers) < 10) {
            $this->answers[] = ['text' => '', 'emoji' => '', 'redirect_url' => ''];
        }
    }

    public function removeAnswer($index)
    {
        if (count($this->answers) > 2) {
            unset($this->answers[$index]);
            $this->answers = array_values($this->answers);
        }
    }

    public function createPoll()
    {
        $this->validate();

        $poll = auth()->user()->polls()->create([
            'question' => $this->question,
            'layout_type' => $this->layout,
            'auto_submit' => $this->autoSubmit,
            'require_email' => $this->requireEmail,
            'collect_feedback' => $this->collectFeedback,
            'thank_you_message' => $this->thankYouMessage,
            'hide_branding' => $this->hideBranding,
            'status' => 'draft',
        ]);

        foreach ($this->answers as $index => $answer) {
            if (!empty($answer['text']) || !empty($answer['emoji'])) {
                $poll->options()->create([
                    'answer_text' => $answer['text'],
                    'answer_emoji' => $answer['emoji'],
                    'redirect_url' => $answer['redirect_url'],
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('polls.show', $poll)
            ->with('success', 'Poll created successfully!');
    }
};
?>
<div class="max-w-4xl mx-auto space-y-6">
    <flux:header>
        <flux:heading>Create New Poll</flux:heading>
        <flux:text>Design your poll with up to 10 answer options.</flux:text>
    </flux:header>

    <form wire:submit="createPoll">
        <!-- Basic Information -->
        <flux:card>
            <flux:card.header>
                <flux:card.title>Basic Information</flux:card.title>
            </flux:card.header>
            <flux:card.content class="space-y-4">
                <flux:input 
                    wire:model="question" 
                    label="Poll Question" 
                    required
                    placeholder="What would you like to ask?"
                />
                
                <flux:radio wire:model="layout" label="Layout">
                    <flux:radio.option value="vertical">Vertical List</flux:radio.option>
                    <flux:radio.option value="horizontal">Horizontal Row</flux:radio.option>
                </flux:radio>
            </flux:card.content>
        </flux:card>

        <!-- Answer Options -->
        <flux:card>
            <flux:card.header>
                <flux:card.title>Answer Options</flux:card.title>
                <flux:button wire:click="addAnswer" type="button" variant="ghost">
                    Add Answer
                </flux:button>
            </flux:card.header>
            <flux:card.content class="space-y-4">
                @foreach($answers as $index => $answer)
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:input 
                                wire:model="answers.{{ $index }}.text" 
                                label="Text"
                                placeholder="Answer text"
                            />
                            <flux:input 
                                wire:model="answers.{{ $index }}.emoji" 
                                label="Emoji"
                                placeholder="👍"
                            />
                            <flux:input 
                                wire:model="answers.{{ $index }}.redirect_url" 
                                label="Redirect URL (optional)"
                                placeholder="https://example.com"
                            />
                        </div>
                        @if(count($answers) > 2)
                            <flux:button 
                                wire:click="removeAnswer({{ $index }})" 
                                type="button" 
                                variant="ghost"
                                class="text-red-600"
                            >
                                Remove
                            </flux:button>
                        @endif
                    </div>
                @endforeach
            </flux:card.content>
        </flux:card>

        <!-- Advanced Options -->
        <flux:card>
            <flux:card.header>
                <flux:card.title>Advanced Options</flux:card.title>
            </flux:card.header>
            <flux:card.content class="space-y-4">
                <flux:checkbox wire:model="autoSubmit">
                    Auto-submit responses (no submit button)
                </flux:checkbox>
                
                <flux:checkbox wire:model="requireEmail">
                    Require email address (for platforms like Gmail)
                </flux:checkbox>
                
                <flux:checkbox wire:model="collectFeedback">
                    Collect free-form feedback after response
                </flux:checkbox>
                
                <flux:checkbox wire:model="hideBranding">
                    Hide Antipoll branding
                </flux:checkbox>
                
                <flux:textarea 
                    wire:model="thankYouMessage" 
                    label="Custom Thank You Message (optional)"
                    placeholder="Thank you for your response!"
                    rows="3"
                />
            </flux:card.content>
        </flux:card>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <flux:button href="{{ route('polls.index') }}" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit">
                Create Poll
            </flux:button>
        </div>
    </form>
</div>
```

### Poll Analytics Page Component
```php
<?php

use App\Models\Poll;
use Livewire\Component;

new class extends Component {
    public Poll $poll;
    public $chartData;
    public $responses;
    public $filters = [
        'dateRange' => null,
        'answer' => null,
        'platform' => null,
        'search' => '',
    ];

    protected $listeners = ['responseReceived' => 'refreshData'];

    public function mount(Poll $poll)
    {
        $this->authorize('view', $poll);
        $this->poll = $poll;
        $this->refreshData();
    }

    public function updatedFilters()
    {
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
<div class="space-y-6">
    <flux:header>
        <flux:heading>{{ $poll->question }} - Analytics</flux:heading>
        <flux:text>View response data and insights for this poll.</flux:text>
    </flux:header>

    <!-- Chart Section -->
    <flux:card>
        <flux:card.header>
            <flux:card.title>Response Distribution</flux:card.title>
        </flux:card.header>
        <flux:card.content>
            <flux:chart type="bar" :data="$chartData">
                <flux:chart.x-axis data-key="label" />
                <flux:chart.y-axis data-key="count" />
                <flux:chart.series name="Responses" data-key="count" />
            </flux:chart>
        </flux:card.content>
    </flux:card>

    <!-- Filters -->
    <flux:card>
        <flux:card.header>
            <flux:card.title>Filters</flux:card.title>
        </flux:card.header>
        <flux:card.content>
            <flux:grid class="grid-cols-1 md:grid-cols-4 gap-4">
                <flux:col>
                    <flux:date-picker wire:model="filters.dateRange" label="Date Range" />
                </flux:col>
                <flux:col>
                    <flux:select wire:model="filters.answer" label="Answer">
                        <option value="">All Answers</option>
                        @foreach($poll->options as $option)
                            <option value="{{ $option->id }}">{{ $option->answer_text }}</option>
                        @endforeach
                    </flux:select>
                </flux:col>
                <flux:col>
                    <flux:select wire:model="filters.platform" label="Platform">
                        <option value="">All Platforms</option>
                        <option value="mailchimp">Mailchimp</option>
                        <option value="gmail">Gmail</option>
                        <option value="kit">Kit</option>
                    </flux:select>
                </flux:col>
                <flux:col>
                    <flux:input wire:model="filters.search" label="Search" placeholder="Search responses..." />
                </flux:col>
            </flux:grid>
        </flux:card.content>
    </flux:card>

    <!-- Export Actions -->
    <flux:card>
        <flux:card.header>
            <flux:card.title>Export Data</flux:card.title>
        </flux:card.header>
        <flux:card.content>
            <flux:grid class="grid-cols-1 md:grid-cols-3 gap-4">
                <flux:col>
                    <flux:button wire:click="exportData('csv')" variant="outline">
                        Export as CSV
                    </flux:button>
                </flux:col>
                <flux:col>
                    <flux:button wire:click="exportData('json')" variant="outline">
                        Export as JSON
                    </flux:button>
                </flux:col>
                <flux:col>
                    <flux:button wire:click="exportData('pdf')" variant="outline">
                        Export as PDF
                    </flux:button>
                </flux:col>
            </flux:grid>
        </flux:card.content>
    </flux:card>
</div>
```

## Layout Configuration

### Main App Layout
```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Antipoll' }}</title>
    @livewireStyles
    @fluxStyles
</head>
<body>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <flux:heading level="3">
                            <a href="{{ route('dashboard') }}">Antipoll</a>
                        </flux:heading>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <flux:button href="{{ route('polls.create') }}" variant="ghost">
                            Create Poll
                        </flux:button>
                        <flux:dropdown>
                            <flux:dropdown.trigger>
                                {{ auth()->user()->name }}
                            </flux:dropdown.trigger>
                            <flux:dropdown.menu>
                                <flux:dropdown.item href="{{ route('billing.dashboard') }}">
                                    {{ auth()->user()->subscription?->name ?? 'Free Trial' }}
                                </flux:dropdown.item>
                                <flux:dropdown.item href="{{ route('settings.profile') }}">
                                    Settings
                                </flux:dropdown.item>
                                <flux:dropdown.item wire:click="logout">
                                    Logout
                                </flux:dropdown.item>
                            </flux:dropdown.menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-6">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @fluxScripts
</body>
</html>
```

## Key Differences from Class-Based Components

### 1. **Single File Structure**
- PHP class and HTML template in one file
- No separate class files needed
- Better component colocation

### 2. **No Namespace Declaration**
- Components don't need namespace declarations
- Direct class definition with `new class extends Component`

### 3. **No Render Method**
- HTML is placed directly after PHP code
- No explicit `render()` method needed
- Layouts are handled automatically by routing

### 4. **Route Model Binding**
- Route parameters automatically passed to `mount()` method
- Laravel's route model binding works seamlessly
- No manual model resolution needed

### 5. **Component Organization**
- `pages::` namespace for full-page components
- Organized in `resources/views/pages/` directory
- Clear separation from reusable components

## Benefits of Livewire 4 Page Components

### 1. **Simplified Development**
- No need to switch between PHP and Blade files
- Everything related to a page in one place
- Faster development workflow

### 2. **Better Organization**
- Clear distinction between pages and components
- Logical file structure
- Easier to find and maintain code

### 3. **Improved Performance**
- Single file loading
- Better caching opportunities
- Reduced file system overhead

### 4. **Enhanced Developer Experience**
- IDE support for both PHP and Blade
- Better code completion
- Easier refactoring

## Best Practices

### 1. **Component Size**
- Keep components focused and manageable
- Split large components into smaller ones
- Use traits for shared functionality

### 2. **State Management**
- Minimize public properties
- Use computed properties for derived data
- Implement proper validation

### 3. **Security**
- Always authorize user actions
- Validate all inputs
- Use route model binding safely

### 4. **Performance**
- Lazy load heavy data
- Implement proper caching
- Use Livewire's lazy loading features

### 5. **Testing**
- Test component logic separately
- Test user interactions
- Use Pest for testing