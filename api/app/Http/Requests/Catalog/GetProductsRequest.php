<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetProductsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filters' => 'nullable|array',
            'filters.*' => 'required|array',
            'filters.*.property_slug' => 'required|string',
            'filters.*.value' => 'nullable|string',
            'filters.*.min' => 'nullable|numeric',
            'filters.*.max' => 'nullable|numeric',
        ];
    }
}
