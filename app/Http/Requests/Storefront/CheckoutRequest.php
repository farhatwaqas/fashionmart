<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
