<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SkillRequest extends FormRequest
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
        $skillId = $this->route('skill');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('skills')->ignore($skillId)
            ],
            'category' => [
                'required',
                'string',
                Rule::in(['frontend', 'backend', 'database', 'devops', 'mobile', 'tools', 'soft_skills', 'other'])
            ],
            'proficiency' => ['required', 'integer', 'min:0', 'max:100'],
            'years_experience' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:255'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Skill name is required',
            'name.unique' => 'This skill already exists',
            'category.required' => 'Skill category is required',
            'category.in' => 'Invalid skill category',
            'proficiency.required' => 'Proficiency level is required',
            'proficiency.min' => 'Proficiency must be at least 0',
            'proficiency.max' => 'Proficiency cannot exceed 100',
            'years_experience.numeric' => 'Years of experience must be a number',
            'color.regex' => 'Color must be a valid hex code (e.g., #FF0000)',
        ];
    }
}
