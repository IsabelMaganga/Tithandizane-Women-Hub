@extends('admin.layouts.admin')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')
@section('page-subtitle', 'Update event details and information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-2xl shadow-lg" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.events.update', $event) }}">
                @method('PUT')
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Event Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" required value="{{ old('title', $event->title) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            placeholder="Enter event title">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Description
                        </label>
                        <textarea name="description" rows="4"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            placeholder="Enter event description">{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" required value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            End Date
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Start Time
                        </label>
                        <input type="time" name="start_time" value="{{ old('start_time', $event->start_time?->format('H:i')) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            End Time
                        </label>
                        <input type="time" name="end_time" value="{{ old('end_time', $event->end_time?->format('H:i')) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Location
                        </label>
                        <input type="text" name="location" value="{{ old('location', $event->location) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            placeholder="Enter event location">
                        @error('location')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Event Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" required
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="general" {{ old('type', $event->type) === 'general' ? 'selected' : '' }}>General</option>
                            <option value="training" {{ old('type', $event->type) === 'training' ? 'selected' : '' }}>Training</option>
                            <option value="workshop" {{ old('type', $event->type) === 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="meeting" {{ old('type', $event->type) === 'meeting' ? 'selected' : '' }}>Meeting</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="upcoming" {{ old('status', $event->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing" {{ old('status', $event->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Event Color <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <label class="flex items-center">
                                <input type="radio" name="color" value="#7c3aed" {{ old('color', $event->color) === '#7c3aed' ? 'checked' : '' }} class="mr-2">
                                <span class="w-6 h-6 rounded-full" style="background: #7c3aed;"></span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="color" value="#3b82f6" {{ old('color', $event->color) === '#3b82f6' ? 'checked' : '' }} class="mr-2">
                                <span class="w-6 h-6 rounded-full" style="background: #3b82f6;"></span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="color" value="#10b981" {{ old('color', $event->color) === '#10b981' ? 'checked' : '' }} class="mr-2">
                                <span class="w-6 h-6 rounded-full" style="background: #10b981;"></span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="color" value="#f59e0b" {{ old('color', $event->color) === '#f59e0b' ? 'checked' : '' }} class="mr-2">
                                <span class="w-6 h-6 rounded-full" style="background: #f59e0b;"></span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="color" value="#ef4444" {{ old('color', $event->color) === '#ef4444' ? 'checked' : '' }} class="mr-2">
                                <span class="w-6 h-6 rounded-full" style="background: #ef4444;"></span>
                            </label>
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Participants -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Max Participants
                        </label>
                        <input type="number" name="max_participants" min="1" value="{{ old('max_participants', $event->max_participants) }}"
                            class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            placeholder="Leave empty for unlimited">
                        @error('max_participants')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('admin.events.index') }}" 
                        class="px-6 py-3 rounded-xl font-medium transition hover:opacity-90"
                        style="background: var(--light-gray); color: var(--text-primary);">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl text-white font-medium transition hover:opacity-90"
                        style="background: var(--purple);">
                        <i class="fas fa-save mr-2"></i>Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
