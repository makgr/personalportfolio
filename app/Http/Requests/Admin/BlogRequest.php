<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
        $postId = $this->route('blog_post') ?? $this->route('id');

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blog_posts')->ignore($postId)
            ],
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,scheduled,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_tags' => 'nullable|array',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Blog title is required',
            'excerpt.required' => 'Blog excerpt is required',
            'content.required' => 'Blog content is required',
            'slug.unique' => 'This slug is already taken',
            'featured_image.max' => 'Featured image must not exceed 2MB',
        ];
    }
}
