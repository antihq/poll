<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::simple'), Title('Sign up')] class extends Component
{
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

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Registration failed. Please try again.',
            ]);
        }

        $result = $user->attemptLoginUsingOneTimePassword($this->one_time_password, remember: true);

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

            $this->redirectIntended('/dashboard', navigate: true);

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

    /**
     * Reset the form.
     */
    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'one_time_password', 'showOtpForm']);

        $this->resetErrorBag();
    }
}; ?>

<div class="isolate flex min-h-dvh items-center justify-center">
    @if (! $showOtpForm)
        <div class="w-full max-w-md rounded-xl bg-white shadow-md ring-1 ring-black/5">
            <div class="p-7 sm:p-11">
                <form wire:submit="sendOtp" class="space-y-8">
                    <div class="flex items-start">
                        <a href="/" wire:navigate>
                            <img src="/logo@2x.png" alt="" class="h-9" />
                        </a>
                    </div>

                    <div>
                        <flux:heading level="1" class="text-base/6! font-medium">Create an account</flux:heading>
                        <flux:text class="mt-1 text-sm/5">
                            Enter your details to create your account and verify your email
                        </flux:text>
                    </div>

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

                    <flux:button variant="primary" color="zinc" type="submit" class="w-full rounded-full!">
                        Create account
                    </flux:button>
                </form>
            </div>
            <div class="m-1.5 rounded-lg bg-zinc-50 py-4 text-center text-sm/5 ring-1 ring-black/5">
                Already have an account?
                <flux:link href="/login" :accent="false" wire:navigate>Log in</flux:link>
            </div>
        </div>
    @else
        <div class="w-full max-w-md rounded-xl bg-white shadow-md ring-1 ring-black/5">
            <div class="p-7 sm:p-11">
                <!-- OTP Verification Form -->
                <form wire:submit="register" class="space-y-8">
                    <div class="flex items-start">
                        <a href="/" wire:navigate>
                            <img src="/logo@2x.png" alt="" class="h-9" />
                        </a>
                    </div>

                    <div>
                        <flux:heading level="1" class="text-base/6! font-medium">Check your email</flux:heading>
                        <flux:text class="mt-2">
                            Then enter the verification code included in the email below:
                        </flux:text>
                    </div>

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
            </div>
        </div>
    @endif
</div>
