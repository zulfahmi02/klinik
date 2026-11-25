<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ReportLoginRequest extends LoginRequest
{
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $login = $this->input('login') ?: $this->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = [$field => $login, 'password' => $this->input('password')];
        $provider = Auth::guard('web')->getProvider();
        $candidate = $provider->retrieveByCredentials($credentials);

        if (! $candidate
            || ($this->user() && $candidate->getAuthIdentifier() !== $this->user()->getAuthIdentifier())
            || ! $provider->validateCredentials($candidate, $credentials)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                $this->filled('login') ? 'login' : 'email' => trans('auth.failed'),
            ]);
        }

        if (! $this->user()) {
            Auth::guard('web')->login($candidate);
        }
        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        return 'reports|'.parent::throttleKey();
    }
}
