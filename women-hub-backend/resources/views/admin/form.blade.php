@php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp

<div class="row g-3">
    {{-- Personal Info --}}
    <div class="col-12">
        <div class="form-card">
            <h6 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:20px;">
                <i class="bi bi-person-circle me-2" style="color:var(--accent)"></i>Personal Information
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $mentor->name ?? '') }}" placeholder="e.g. Grace Banda" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $mentor->email ?? '') }}" placeholder="grace@example.com" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone', $mentor->phone ?? '') }}" placeholder="+265 999 000 000">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if(!empty($mentor->photo))
                        <small class="text-muted d-block mt-1">Current photo: {{ $mentor->photo }}. Leave blank to keep it.</small>
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Bio / About <span class="text-danger">*</span></label>
                    <textarea name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror"
                              placeholder="Brief background, experience, and what this mentor brings to the platform…" required>{{ old('bio', $mentor->bio ?? '') }}</textarea>
                    @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Mentorship Details --}}
    <div class="col-12">
        <div class="form-card">
            <h6 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:20px;">
                <i class="bi bi-journal-bookmark-fill me-2" style="color:var(--accent)"></i>Mentorship Details
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Area of Support <span class="text-danger">*</span></label>
                    <select name="area_of_support" class="form-select @error('area_of_support') is-invalid @enderror" required>
                        <option value="">— Select area —</option>
                        <option value="menstrual_hygiene" {{ old('area_of_support', $mentor->area_of_support ?? '') == 'menstrual_hygiene' ? 'selected' : '' }}>Menstrual Hygiene</option>
                        <option value="general_issues"    {{ old('area_of_support', $mentor->area_of_support ?? '') == 'general_issues'    ? 'selected' : '' }}>General Issues</option>
                        <option value="both"              {{ old('area_of_support', $mentor->area_of_support ?? '') == 'both'              ? 'selected' : '' }}>Both</option>
                    </select>
                    @error('area_of_support') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active"   {{ old('status', $mentor->status ?? 'active') == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $mentor->status ?? '')       == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Available Days --}}
                <div class="col-12">
                    <label class="form-label">Available Days <span class="text-danger">*</span></label>
                    @php $selectedDays = old('available_days', $mentor->available_days ?? []); @endphp
                    <div>
                        @foreach($days as $day)
                            <input type="checkbox" name="available_days[]" value="{{ $day }}"
                                   id="day_{{ $day }}" class="day-check"
                                   {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                            <label for="day_{{ $day }}" class="day-label">{{ substr($day,0,3) }}</label>
                        @endforeach
                    </div>
                    @error('available_days') <div class="text-danger" style="font-size:.78rem;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                {{-- Time --}}
                <div class="col-md-4">
                    <label class="form-label">Available From <span class="text-danger">*</span></label>
                    <input type="time" name="available_time_from" class="form-control @error('available_time_from') is-invalid @enderror"
                           value="{{ old('available_time_from', $mentor->available_time_from ?? '08:00') }}" required>
                    @error('available_time_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Available To <span class="text-danger">*</span></label>
                    <input type="time" name="available_time_to" class="form-control @error('available_time_to') is-invalid @enderror"
                           value="{{ old('available_time_to', $mentor->available_time_to ?? '17:00') }}" required>
                    @error('available_time_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>
</div>