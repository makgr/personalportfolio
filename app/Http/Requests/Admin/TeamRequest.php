<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $teamId = $this->route('team');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('teams')->ignore($teamId)
            ],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['nullable', Rule::in(['engineering', 'design', 'marketing', 'sales', 'hr', 'management', 'other'])],
            'bio' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:1024'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'string'],
            'join_date' => ['nullable', 'date'],
            'employment_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'intern'])],
            'location' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Team member name is required',
            'position.required' => 'Position is required',
            'slug.unique' => 'This slug is already taken',
            'image.max' => 'Image must not exceed 1MB',
            'email.email' => 'Please provide a valid email address',
            'color.regex' => 'Color must be a valid hex color code',
        ];
    }
}