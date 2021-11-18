<?php
namespace Timedoor\TmdMembership\traits;

use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

trait AuthenticateUsers
{

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): \Illuminate\Database\Eloquent\Model
    {
        $this->ensureIsNotRateLimited();

        $user = $this->model()
                    ->attempt($this, $this->usernames(), $this->fields())
                    ->first();

        if ($user) {
            if (!Hash::check($this->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        
        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            $this->username() => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
    
    /**
     * username
     *
     * @return void
     */
    protected function username()
    {
        return $this->usernames()['default'] ?? 'email';
    }
    
    /**
     * usernames
     *
     * @return Array
     */
    protected function usernames() : Array
    {
        return config('tmd-membership.username.keys');
    }
    
    /**
     * fields
     *
     * @return Array
     */
    protected function fields() : Array
    {
        $fields = config('tmd-membership.username.fields');
        $fields = is_string($fields) ? explode('|', $fields) : $fields;

        foreach ($fields as &$field) {
            if (strpos($field, ',') !== false) {
                $field = explode(',', $field);
            }
        }

        return $fields;
    }
    
    /**
     * guard
     *
     * @return String
     */
    protected function guard(): String
    {
        return config('tmd-membership.guard') ?? 'web';
    }
    
    /**
     * model
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function model(): \Illuminate\Database\Eloquent\Model
    {
        $authConfig = config('auth');
        $provider   = $authConfig['guards'][$this->guard()]['provider'];
        $model      = $authConfig['providers'][$provider]['model'];

        return new $model;
    }
}