<?php

use App\Models\User;
use App\Models\Team;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;


new #[Layout('layouts::simple')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string|digits:6')]
    public string $one_time_password = '';

    public bool $showOtpForm = false;

    /**
     * Send OTP for registration.
     */
    public function sendOtp(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
        ]);

        $this->ensureIsNotRateLimited();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $user->sendOneTimePassword();

        $this->showOtpForm = true;

        $this->reset('one_time_password');
    }

    /**
     * Handle OTP verification and complete registration.
     */
    public function register(): void
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Registration failed. Please try again.',
            ]);
        }

        $result = $user->attemptLoginUsingOneTimePassword($this->one_time_password);

        if ($result->isOk()) {
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            $user->markEmailAsVerified();

            Team::create([
                'name' => $user->name,
                'user_id' => $user->id,
                'personal' => true,
            ]);

            event(new Registered($user));

            Auth::login($user);

            $this->redirectIntended(route('dashboard', absolute: false), navigate: true);

            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'one_time_password' => $result->validationMessage(),
        ]);
    }

    /**
     * Ensure OTP request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
        ]);
    }

    /**
     * Get the OTP rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    /**
     * Reset the form.
     */
    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'one_time_password', 'showOtpForm']);

        $this->resetErrorBag();
    }
}; ?>

<div class="mx-auto flex h-full max-w-sm flex-col justify-center gap-6">
    @if (!$showOtpForm)
        <div class="text-center">
            <flux:heading class="text-xl">Create an account</flux:heading>
            <flux:text class="mt-2">Enter your details to create your account and verify your email</flux:text>
        </div>

        <form wire:submit="sendOtp" class="space-y-6">
            <flux:input
                wire:model="name"
                label="Name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Full name"
            />

            <flux:input
                wire:model="email"
                label="Email address"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">Create account</flux:button>
            </div>
        </form>

        <flux:text class="space-x-1 text-center rtl:space-x-reverse">
            <span>Already have an account?</span>
            <flux:link :href="route('login')" wire:navigate>Log in</flux:link>
        </flux:text>
    @else
        <!-- OTP Verification Form -->
        <form wire:submit="register" class="space-y-6">
            <div class="text-center">
                <flux:heading class="text-xl">Check your email</flux:heading>
                <flux:text class="mt-2">Then enter the verification code included in the email below:</flux:text>
            </div>

            <!-- One-Time Password -->
            <div class="text-center">
                <flux:otp
                    wire:model="one_time_password"
                    label="One-time password"
                    description:trailing="The code you receive will work for 15 minutes."
                    length="6"
                    submit="auto"
                    class="mx-auto"
                />

                <flux:error name="email" />
            </div>
        </form>
    @endif
</div>
