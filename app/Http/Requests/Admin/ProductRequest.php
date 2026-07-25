<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($productId)],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'featured' => ['sometimes', 'boolean'],
            'hot_selling' => ['sometimes', 'boolean'],
            'recommended' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'hot_selling' => $this->boolean('hot_selling'),
            'recommended' => $this->boolean('recommended'),
        ]);
    }
}
