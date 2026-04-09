<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;


class PasswordRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Password must be a string');

            return;
        }

        if (strlen($value) < 8) {
            $fail('Password must be at least 8 characters long');
        }

        if (!preg_match('/^[A-Za-z\d\W_]+$/', $value)) {
            $fail('Password must contain only Latin letters, digits, and the special characters');
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $fail('Password must contain at least one uppercase letter');
        }

        if (!preg_match('/\d/', $value)) {
            $fail('Password must contain at least one digit');
        }

        if (!preg_match('/[!@#$%^&*]/', $value)) {
            $fail('Password must contain at least one special character');
        }
    }
}