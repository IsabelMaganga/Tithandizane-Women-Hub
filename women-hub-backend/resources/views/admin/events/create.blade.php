@extends('admin.layouts.admin')

@section('title', 'Create Event')
@section('page-title', 'Create New Event')
@section('page-subtitle', 'Add a new event, training session, or workshop to the calendar')

@push('styles')
    <style>
        /* ── Form card ── */
        .form-card {
            background: var(--card-bg);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.25s ease;
        }

        .form-card:hover {
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.07);
        }

        /* ── Form fields ── */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
        }

        .form-label .required {
            color: var(--red);
            margin-left: 2px;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 12px;
            border: 1.5px solid var(--border-color);
            background: var(--input-bg);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            appearance: none;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--purple);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
        }

        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .form-error {
            display: block;
            margin-top: 0.3rem;
            font-size: 0.8rem;
            color: var(--red);
        }

        /* ── Color picker ── */
        .color-options {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            padding-top: 0.25rem;
        }

        .color-option {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            padding: 0.3rem 0.6rem 0.3rem 0.3rem;
            border-radius: 40px;
            border: 2px solid transparent;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .color-option:hover {
            background: var(--bg-secondary);
        }

        .color-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .color-option .color-swatch {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid var(--border-color);
            transition: transform 0.2s ease, border-color 0.2s ease;
            flex-shrink: 0;
        }

        .color-option input[type="radio"]:checked+.color-swatch {
            transform: scale(1.1);
            border-color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
        }

        .color-option .color-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
            display: none;
        }

        .color-option input[type="radio"]:checked~.color-label {
            display: inline;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.7rem 1.8rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-cancel {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1.5px solid var(--border-color);
        }

        .btn-cancel:hover {
            background: var(--border-color);
            color: var(--text-primary);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--purple);
            color: #fff;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);
        }

        .btn-primary:hover {
            background: var(--purple-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.30);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .form-card .p-8 {
                padding: 1.5rem !important;
            }

            .color-option .color-label {
                display: inline !important;
                font-size: 0.7rem;
            }

            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
                flex: 1;
            }

            .form-actions {
                flex-direction: column-reverse;
                gap: 0.75rem;
            }

            .form-actions .btn {
                width: 100%;
            }
        }

        @media (min-width: 641px) {
            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="form-card">
            <div class="p-8">
                <form method="POST" action="{{ route('admin.events.store') }}" novalidate>
                    @csrf

                    {{-- ─── Grid ─── --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1">

                        {{-- Title (full width) --}}
                        <div class="md:col-span-2 form-group">
                            <label class="form-label" for="title">
                                Event Title <span class="required">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="e.g., Advanced Laravel Workshop"
                                   value="{{ old('title') }}">
                            @error('title')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Description (full width) --}}
                        <div class="md:col-span-2 form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Provide details about this event…">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Start Date --}}
                        <div class="form-group">
                            <label class="form-label" for="start_date">
                                Start Date <span class="required">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" required
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date') }}">
                            @error('start_date')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- End Date --}}
                        <div class="form-group">
                            <label class="form-label" for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}">
                            @error('end_date')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Start Time --}}
                        <div class="form-group">
                            <label class="form-label" for="start_time">Start Time</label>
                            <input type="time" name="start_time" id="start_time"
                                   class="form-control @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time') }}">
                            @error('start_time')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- End Time --}}
                        <div class="form-group">
                            <label class="form-label" for="end_time">End Time</label>
                            <input type="time" name="end_time" id="end_time"
                                   class="form-control @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time') }}">
                            @error('end_time')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Location (full width) --}}
                        <div class="md:col-span-2 form-group">
                            <label class="form-label" for="location">Location</label>
                            <input type="text" name="location" id="location"
                                   class="form-control @error('location') is-invalid @enderror"
                                   placeholder="e.g., Conference Room A, Online (Zoom)"
                                   value="{{ old('location') }}">
                            @error('location')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div class="form-group">
                            <label class="form-label" for="type">
                                Event Type <span class="required">*</span>
                            </label>
                            <select name="type" id="type" required
                                    class="form-control @error('type') is-invalid @enderror">
                                <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>Training</option>
                                <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                <option value="meeting" {{ old('type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                            </select>
                            @error('type')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label class="form-label" for="status">
                                Status <span class="required">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="form-control @error('status') is-invalid @enderror">
                                <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Max Participants --}}
                        <div class="form-group">
                            <label class="form-label" for="max_participants">Max Participants</label>
                            <input type="number" name="max_participants" id="max_participants" min="1"
                                   class="form-control @error('max_participants') is-invalid @enderror"
                                   placeholder="Unlimited"
                                   value="{{ old('max_participants') }}">
                            @error('max_participants')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Color --}}
                        <div class="form-group">
                            <label class="form-label">
                                Event Color <span class="required">*</span>
                            </label>
                            <div class="color-options">
                                @php
                                    $colors = [
                                        '#7c3aed' => 'Purple',
                                        '#3b82f6' => 'Blue',
                                        '#10b981' => 'Green',
                                        '#f59e0b' => 'Amber',
                                        '#ef4444' => 'Red',
                                        '#ec4899' => 'Pink',
                                        '#8b5cf6' => 'Violet',
                                    ];
                                    $oldColor = old('color', '#7c3aed');
                                @endphp
                                @foreach ($colors as $hex => $name)
                                    <label class="color-option">
                                        <input type="radio" name="color" value="{{ $hex }}"
                                               {{ $oldColor == $hex ? 'checked' : '' }}>
                                        <span class="color-swatch" style="background: {{ $hex }};"></span>
                                        <span class="color-label">{{ $name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('color')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>{{-- end grid --}}

                    {{-- ─── Actions ─── --}}
                    <div class="mt-8 pt-6 border-t" style="border-color: var(--border-color);">
                        <div class="form-actions">
                            <a href="{{ route('admin.events.index') }}" class="btn btn-cancel">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Create Event
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection