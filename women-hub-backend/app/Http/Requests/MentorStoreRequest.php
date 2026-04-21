<?php
// app/Http/Requests/MentorStoreRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MentorStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all admin users
    }

    public function rules()
    {
        return [
            // Basic Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email',
            'password' => 'required|string|min:12|confirmed',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Professional Information
            'expertise' => 'required|array|min:1',
            'expertise.*' => 'string',
            'bio' => 'required|string|max:500',
            'status' => 'nullable|in:active,pending,inactive',
            'availability' => 'nullable|string|max:255',
            'available_days' => 'nullable|array',
            'available_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'available_time_start' => 'nullable|string|max:5',
            'available_time_end' => 'nullable|string|max:5',
            
            // Social & Professional Profiles
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            
            // Additional Information
            'notes' => 'nullable|string',
            'notify_welcome' => 'nullable|boolean',
            'notify_training' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            // Basic Information Messages
            'name.required' => 'The mentor\'s full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered as a mentor.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 12 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            
            // Professional Information Messages
            'expertise.required' => 'Please select at least one area of expertise.',
            'expertise.min' => 'Please select at least one area of expertise.',
            'bio.required' => 'Professional bio is required.',
            'bio.max' => 'Bio cannot exceed 500 characters.',
            'available_days.*.in' => 'Invalid day selected for availability.',
            
            // Photo Messages
            'photo.image' => 'Profile photo must be an image file.',
            'photo.max' => 'Profile photo cannot exceed 2MB.',
            
            // Social Links Messages
            'linkedin_url.url' => 'Please enter a valid LinkedIn URL.',
            'twitter_url.url' => 'Please enter a valid Twitter URL.',
            'website_url.url' => 'Please enter a valid website URL.',
        ];
    }
}