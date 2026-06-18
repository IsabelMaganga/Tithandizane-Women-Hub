@extends('mentor.layouts.dashboard')

@section('title') Guidance @endsection

@section('content')
<div class="px-4 py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="mt-10 mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Guidance Content</h1>
        <p class="mt-2 text-sm text-gray-600">Publish and manage educational content for app users. Choose a category below to create, edit, publish, or unpublish your articles.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="p-6 transition-shadow bg-white border border-purple-100 rounded-2xl shadow-sm hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-purple-50">
                    <i class="text-purple-700 fa-solid fa-droplet"></i>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Menstrual Hygiene Guidance</h3>
            </div>
            <p class="mb-5 text-sm text-gray-600">Share hygiene tips, cycle care, and health guidance for menstrual wellness.</p>
            <a href="{{ route('mentor.hygiene') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-700 rounded-lg hover:bg-purple-800">
                Manage Content
                <i class="ml-2 fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <div class="p-6 transition-shadow bg-white border border-purple-100 rounded-2xl shadow-sm hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-violet-50">
                    <i class="text-violet-700 fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">General Issues</h3>
            </div>
            <p class="mb-5 text-sm text-gray-600">Cover self-esteem, stress, relationships, health, and personal development topics.</p>
            <a href="{{ route('mentor.general') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-700 rounded-lg hover:bg-purple-800">
                Manage Content
                <i class="ml-2 fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection
