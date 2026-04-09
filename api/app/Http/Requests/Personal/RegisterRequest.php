<?php

namespace App\Http\Requests\Personal;

use App\Rules\EmailRule;
use App\Rules\PasswordRule;
use App\Support\Enums\GenderEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', new EmailRule(), 'unique:users,email'],
            'password' => ['required', new PasswordRule()],
            'gender' => ['required', new Enum(GenderEnum::class)],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
