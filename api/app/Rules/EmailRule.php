<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;


class EmailRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('Invalid :attribute format');
        }
    }
}