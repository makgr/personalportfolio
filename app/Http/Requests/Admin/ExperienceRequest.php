<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExperienceRequest extends FormRequest
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
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'employment_type' => [
                'nullable',
                'string',
                Rule::in(['full-time', 'part-time', 'contract', 'freelance', 'internship'])
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'array'],
            'responsibilities.*' => ['string', 'max:500'],
            'achievements' => ['nullable', 'array'],
            'achievements.*' => ['string', 'max:500'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string', 'max:50'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:today',
                Rule::requiredIf(fn() => !$this->boolean('is_current'))
            ],
            'is_current' => ['boolean'],
            'company_logo' => ['nullable', 'image', 'max:1024'],
            'company_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Company name is required',
            'position.required' => 'Position title is required',
            'employment_type.in' => 'Invalid employment type selected',
            'description.required' => 'Job description is required',
            'start_date.required' => 'Start date is required',
            'start_date.before_or_equal' => 'Start date cannot be in the future',
            'end_date.after_or_equal' => 'End date must be after start date',
            'end_date.before_or_equal' => 'End date cannot be in the future',
            'company_logo.max' => 'Company logo must not exceed 1MB',
            'company_url.url' => 'Please provide a valid company URL',
        ];
    }
}