<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'bio' => ['required', 'string'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.platform' => ['required_with:social_links.*', 'string', 'max:50'],
            'social_links.*.url' => ['required_with:social_links.*', 'url', 'max:255'],
            'social_links.*.username' => ['nullable', 'string', 'max:100'],
            'meta_tags' => ['nullable', 'array'],
            'meta_tags.title' => ['nullable', 'string', 'max:60'],
            'meta_tags.description' => ['nullable', 'string', 'max:160'],
            'meta_tags.keywords' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Full name is required',
            'title.required' => 'Professional title is required',
            'bio.required' => 'Bio is required',
            'short_bio.max' => 'Short bio cannot exceed 500 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email address',
            'avatar.max' => 'Avatar image must not exceed 2MB',
            'resume.mimes' => 'Resume must be a PDF or Word document',
            'resume.max' => 'Resume file must not exceed 5MB',
            'social_links.*.platform.required_with' => 'Platform is required for social links',
            'social_links.*.url.required_with' => 'URL is required for social links',
            'social_links.*.url.url' => 'Please provide valid URLs for social links',
            'meta_tags.title.max' => 'Meta title cannot exceed 60 characters',
            'meta_tags.description.max' => 'Meta description cannot exceed 160 characters',
        ];
    }
}