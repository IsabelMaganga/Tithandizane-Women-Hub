@extends('admin.layouts.admin')

@section('title', 'Email Templates')
@section('page-title', 'Email Templates')
@section('page-subtitle', 'Customize system email notifications and templates.')

@section('content')
<div class="px-4 py-0 mx-auto max-w-4xl sm:px-6 lg:px-8">
    <div class="mb-6">
    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #7c3aed; color: white; hover:background: #6d28d9;">
        <i class="fas fa-arrow-left"></i> Back to Settings
    </a>
</div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200" style="background: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b" style="border-color: var(--border-color);">
            <h2 class="text-xl font-bold" style="color: var(--text-primary);">Email Templates</h2>
        </div>
        <div class="divide-y" style="border-color: var(--border-color);">
            @forelse($templates ?? [] as $template)
                <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition" style="background: var(--card-bg);">
                    <div>
                        <h3 class="font-semibold" style="color: var(--text-primary);">{{ $template->name }}</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $template->description }}</p>
                    </div>
                    <button onclick="document.getElementById('editTemplateModal{{ $loop->index }}').style.display='flex'" class="px-3 py-1.5 rounded-lg text-sm" style="background: var(--light-blue); color: var(--blue);">
                        Edit
                    </button>
                </div>
            @empty
                <div class="p-6 text-center" style="color: var(--text-secondary);">
                    <p>No email templates configured.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection