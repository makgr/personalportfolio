<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'max:2048'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string', 'max:100'],
            'starting_price' => ['nullable', 'numeric', 'min:0'],
            'price_unit' => ['nullable', 'string', Rule::in(['hour', 'project', 'month', 'year'])],
            'is_active' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Service title is required',
            'short_description.required' => 'Short description is required',
            'short_description.max' => 'Short description cannot exceed 500 characters',
            'description.required' => 'Service description is required',
            'image.max' => 'Service image must not exceed 2MB',
            'gallery.*.max' => 'Gallery images must not exceed 2MB each',
            'starting_price.numeric' => 'Starting price must be a valid number',
            'starting_price.min' => 'Starting price cannot be negative',
            'price_unit.in' => 'Invalid price unit selected',
        ];
    }
}
