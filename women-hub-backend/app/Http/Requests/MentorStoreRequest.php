<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MentorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all admin requests
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'password' => 'required|string|min:12|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'expertise' => 'required|array|min:1',
            'expertise.*' => 'string',
            'bio' => 'required|string|max:500',
            'status' => 'required|in:pending,active,inactive',
            'availability' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'notify_welcome' => 'boolean',
            'notify_training' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The mentor\'s full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 12 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'expertise.required' => 'Please select at least one area of expertise.',
            'bio.required' => 'Professional bio is required.',
            'linkedin_url.url' => 'Please enter a valid LinkedIn URL.',
            'twitter_url.url' => 'Please enter a valid Twitter URL.',
            'website_url.url' => 'Please enter a valid website URL.',
        ];
    }
}