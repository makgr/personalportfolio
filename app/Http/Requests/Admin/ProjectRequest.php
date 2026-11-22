<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Drop blank feature inputs the form always submits.
     */
    protected function prepareForValidation(): void
    {
        if (is_array($this->input('features'))) {
            $this->merge([
                'features' => array_values(array_filter($this->input('features'), 'filled')),
            ]);
        }
    }

    public function rules()
    {
        $projectId = $this->route('project');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects')->ignore($projectId),
            ],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'max:2048'],
            'type' => ['required', Rule::in(['web', 'mobile', 'desktop', 'design', 'other'])],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_date' => ['nullable', 'date'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'tech_stack' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'duration_weeks' => ['nullable', 'integer', 'min:1'],
            'team_size' => ['nullable', 'string', 'max:50'],
            'my_role' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'meta_tags' => ['nullable', 'array'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Project title is required',
            'slug.unique' => 'This slug is already taken',
            'featured_image.max' => 'Featured image must not exceed 2MB',
            'gallery.*.max' => 'Gallery images must not exceed 2MB each',
        ];
    }
}
