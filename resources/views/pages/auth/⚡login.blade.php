<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::simple'), Title('Login')] class extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string|digits:6')]
    public string $one_time_password = '';

    public bool $showOtpForm = false;

    /**
     * Send OTP to user email.
     */
    public function sendOtp(): void
    {
        $this->validate([
            'email' => 'required|string|email',
        ]);

        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            // Don't reveal if user exists or not for security
            $this->showOtpForm = true;

            $this->reset('one_time_password');

            return;
        }

        $user->sendOneTimePassword();

        $this->showOtpForm = true;

        $this->reset('one_time_password');
    }

    /**
     * Handle OTP login.
     */
    public function login(): void
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $result = $user->attemptLoginUsingOneTimePassword($this->one_time_password);

        if ($result->isOk()) {
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            Auth::login($user);

            $this->redirectIntended(default: '/dashboard', navigate: true);

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
            'email' => 'Too many login attempts. Please try again in '.ceil($seconds / 60).' minutes.',
        ]);
    }

    /**
     * Get the OTP rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="mx-auto flex h-full max-w-sm flex-col justify-center gap-6">
    @if (! $showOtpForm)
        <div class="text-center">
            <flux:heading class="text-xl">Log in to your account</flux:heading>
            <flux:text class="mt-2">Enter your email to receive a one-time password</flux:text>
        </div>

        <!-- Email Form -->
        <form wire:submit="sendOtp" class="space-y-6 text-center">
            <!-- Email Address -->
            <flux:input
                wire:model="email"
                label="Email address"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <flux:button variant="primary" type="submit" class="w-full">Send One-Time Password</flux:button>
        </form>

        <flux:text class="space-x-1 text-center rtl:space-x-reverse">
            <span>Don't have an account?</span>
            <flux:link href="/register" wire:navigate>Sign up</flux:link>
        </flux:text>
    @else
        <!-- OTP Form -->
        <form wire:submit="login" class="space-y-6">
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
