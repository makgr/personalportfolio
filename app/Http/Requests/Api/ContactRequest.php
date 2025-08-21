<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'budget' => ['nullable', 'string', 'max:100'],
            'project_type' => ['nullable', 'string', 'max:100'],
            'timeline' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'email.max' => 'Email cannot exceed 255 characters',
            'subject.max' => 'Subject cannot exceed 255 characters',
            'message.required' => 'Message is required',
            'message.max' => 'Message cannot exceed 2000 characters',
            'phone.max' => 'Phone number cannot exceed 20 characters',
            'company.max' => 'Company name cannot exceed 255 characters',
            'website.url' => 'Please provide a valid website URL',
            'website.max' => 'Website URL cannot exceed 255 characters',
        ];
    }
}
