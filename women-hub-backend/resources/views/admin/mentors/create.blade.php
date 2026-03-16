{{-- resources/views/admin/mentors/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Add New Mentor')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --forest:     #1a3d2b;
    --forest-mid: #2d5a3d;
    --forest-lt:  #3d7a54;
    --gold:       #c8963e;
    --gold-lt:    #e8b86d;
    --gold-pale:  #fdf6ec;
    --cream:      #faf8f4;
    --ink:        #1c1917;
    --muted:      #78716c;
    --border:     #e8e0d4;
    --white:      #ffffff;
    --error:      #b91c1c;
    --success:    #15803d;
    --radius-sm:  8px;
    --radius-md:  14px;
    --radius-lg:  22px;
    --shadow-card: 0 4px 24px rgba(26,61,43,.10), 0 1px 4px rgba(26,61,43,.06);
    --shadow-float: 0 12px 40px rgba(26,61,43,.18);
    --transition: .22s cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; }

.mentor-page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--cream);
    min-height: 100vh;
    padding: 0 0 60px;
}

/* ── Hero Banner ── */
.mentor-hero {
    background: linear-gradient(135deg, var(--forest) 0%, var(--forest-mid) 55%, #1e4d35 100%);
    padding: 36px 40px 80px;
    position: relative;
    overflow: hidden;
}

.mentor-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(ellipse 600px 300px at 90% 50%, rgba(200,150,62,.13) 0%, transparent 70%),
        url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='30' cy='30' r='1' fill='rgba(255,255,255,.04)'/%3E%3C/svg%3E");
    pointer-events: none;
}

.mentor-hero::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 50px;
    background: var(--cream);
    clip-path: ellipse(55% 100% at 50% 100%);
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(200,150,62,.18);
    border: 1px solid rgba(200,150,62,.35);
    border-radius: 40px;
    padding: 5px 14px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--gold-lt);
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: 16px;
}

.hero-badge svg { opacity: .8; }

.hero-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    font-size: 13px;
}

.hero-breadcrumb a {
    color: rgba(255,255,255,.55);
    text-decoration: none;
    transition: color var(--transition);
}

.hero-breadcrumb a:hover { color: var(--gold-lt); }
.hero-breadcrumb .sep { color: rgba(255,255,255,.25); font-size: 11px; }
.hero-breadcrumb .current { color: rgba(255,255,255,.85); font-weight: 500; }

.hero-content {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}

.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    font-weight: 700;
    color: var(--white);
    line-height: 1.15;
    margin: 0 0 8px;
}

.hero-title em {
    font-style: italic;
    color: var(--gold-lt);
}

.hero-subtitle {
    font-size: 14.5px;
    color: rgba(255,255,255,.62);
    font-weight: 400;
    margin: 0;
    max-width: 420px;
    line-height: 1.6;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: var(--radius-sm);
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.18);
    color: rgba(255,255,255,.85);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    text-decoration: none;
    transition: all var(--transition);
    backdrop-filter: blur(8px);
    flex-shrink: 0;
}

.btn-back:hover {
    background: rgba(255,255,255,.18);
    color: white;
    transform: translateX(-2px);
}

/* ── Main Layout ── */
.mentor-layout {
    max-width: 1060px;
    margin: -44px auto 0;
    padding: 0 24px;
    position: relative;
    z-index: 2;
}

/* ── Alert ── */
.alert-errors {
    background: #fff5f5;
    border: 1px solid #fecaca;
    border-left: 4px solid var(--error);
    border-radius: var(--radius-md);
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
    animation: slideIn .3s ease;
}

.alert-errors .alert-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fee2e2;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: var(--error);
}

.alert-errors h6 {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--error);
    margin: 0 0 6px;
}

.alert-errors ul {
    margin: 0;
    padding-left: 16px;
    font-size: 13px;
    color: #7f1d1d;
    line-height: 1.7;
}

@keyframes slideIn {
    from { opacity:0; transform: translateY(-8px); }
    to   { opacity:1; transform: translateY(0); }
}

/* ── Card ── */
.form-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header-strip {
    height: 4px;
    background: linear-gradient(90deg, var(--forest), var(--forest-lt), var(--gold));
}

.card-section {
    padding: 36px 40px;
    border-bottom: 1px solid var(--border);
}

.card-section:last-of-type { border-bottom: none; }

.section-label {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 28px;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--forest), var(--forest-lt));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(26,61,43,.2);
}

.section-icon svg { color: white; }

.section-label-text h3 {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 2px;
}

.section-label-text p {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0;
}

/* ── Profile Photo Zone ── */
.photo-zone {
    display: flex;
    align-items: center;
    gap: 28px;
    padding: 22px 26px;
    background: linear-gradient(135deg, #f0f7f2 0%, var(--gold-pale) 100%);
    border: 1.5px dashed #b5d4bf;
    border-radius: var(--radius-md);
    margin-bottom: 32px;
    cursor: pointer;
    transition: all var(--transition);
}

.photo-zone:hover {
    border-color: var(--forest-lt);
    background: linear-gradient(135deg, #e6f2ea 0%, #fef3e2 100%);
}

.photo-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--forest), var(--forest-lt));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 3px solid white;
    box-shadow: 0 6px 20px rgba(26,61,43,.25);
    overflow: hidden;
    position: relative;
}

.photo-avatar img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: none;
    position: absolute; inset: 0;
}

.photo-avatar .initials {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    color: rgba(255,255,255,.9);
}

.photo-info h4 {
    font-size: 15px;
    font-weight: 700;
    color: var(--forest);
    margin: 0 0 4px;
}

.photo-info p {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0 0 12px;
    line-height: 1.5;
}

.btn-choose-photo {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: white;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--forest);
    cursor: pointer;
    transition: all var(--transition);
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}

.btn-choose-photo:hover {
    border-color: var(--forest);
    background: #f0f7f2;
    box-shadow: 0 2px 8px rgba(26,61,43,.12);
}

/* ── Form Grid ── */
.form-grid { display: grid; gap: 22px; }
.grid-2    { grid-template-columns: 1fr 1fr; }
.grid-3    { grid-template-columns: 1fr 1fr 1fr; }
.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }

/* ── Field ── */
.field { display: flex; flex-direction: column; gap: 7px; }

.field label {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: .03em;
    display: flex;
    align-items: center;
    gap: 4px;
}

.field label .req { color: var(--gold); }
.field label .opt { color: var(--muted); font-weight: 400; font-size: 11.5px; }

.field-wrap { position: relative; }

.field-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #a8a29e;
    pointer-events: none;
    transition: color var(--transition);
}

.has-icon input,
.has-icon select,
.has-icon textarea { padding-left: 40px !important; }

.fc {
    width: 100%;
    padding: 11px 15px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    color: var(--ink);
    background: #fdfdfc;
    outline: none;
    transition: all var(--transition);
    -webkit-appearance: none;
}

.fc::placeholder { color: #c4bcb4; }

.fc:focus {
    border-color: var(--forest-lt);
    background: white;
    box-shadow: 0 0 0 3.5px rgba(61,122,84,.12);
}

.fc:focus ~ .field-icon,
.field-wrap:focus-within .field-icon { color: var(--forest-lt); }

.fc.is-invalid { border-color: var(--error); background: #fff5f5; }
.fc.is-invalid:focus { box-shadow: 0 0 0 3.5px rgba(185,28,28,.1); }

textarea.fc { resize: vertical; min-height: 110px; line-height: 1.65; }

select.fc {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%23a8a29e' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 13px center;
    padding-right: 36px;
    cursor: pointer;
}

.field-hint { font-size: 12px; color: #a8a29e; line-height: 1.5; }

.invalid-feedback {
    font-size: 12px;
    color: var(--error);
    display: flex;
    align-items: center;
    gap: 4px;
}

/* ── Status Radio Cards ── */
.status-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.status-card input[type="radio"] { display: none; }

.status-card label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer;
    text-align: center;
    transition: all var(--transition);
    background: #fdfdfc;
}

.status-card label:hover { border-color: var(--forest-lt); background: #f0f7f2; }

.status-card input[type="radio"]:checked + label {
    border-color: var(--forest);
    background: linear-gradient(135deg, #f0f7f2, #fafdfb);
    box-shadow: 0 2px 12px rgba(26,61,43,.12);
}

.status-dot {
    width: 14px; height: 14px;
    border-radius: 50%;
    margin-bottom: 2px;
}

.dot-pending  { background: #f59e0b; box-shadow: 0 0 0 4px rgba(245,158,11,.15); }
.dot-active   { background: #16a34a; box-shadow: 0 0 0 4px rgba(22,163,74,.15); }
.dot-review   { background: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,.15); }
.dot-rejected { background: #ef4444; box-shadow: 0 0 0 4px rgba(239,68,68,.15); }

.status-card .s-label {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: .02em;
}

.status-card .s-hint {
    font-size: 11px;
    color: var(--muted);
    line-height: 1.4;
}

/* ── Availability Pills ── */
.avail-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.avail-card input[type="radio"] { display: none; }

.avail-card label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 16px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--muted);
    transition: all var(--transition);
    background: #fdfdfc;
}

.avail-card label:hover { border-color: var(--forest-lt); color: var(--forest); }

.avail-card input:checked + label {
    border-color: var(--forest);
    background: linear-gradient(135deg, #f0f7f2, white);
    color: var(--forest);
    font-weight: 700;
    box-shadow: 0 2px 10px rgba(26,61,43,.1);
}

.avail-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}

/* ── Footer ── */
.form-footer {
    padding: 26px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8faf9 0%, var(--gold-pale) 100%);
    border-top: 1px solid var(--border);
}

.footer-note {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    color: var(--muted);
}

.footer-actions { display: flex; gap: 12px; }

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: var(--radius-sm);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition);
    border: none;
    text-decoration: none;
    white-space: nowrap;
}

.btn-reset {
    background: white;
    color: var(--muted);
    border: 1.5px solid var(--border);
}

.btn-reset:hover {
    background: #f5f4f2;
    color: var(--ink);
    border-color: #d6cfc6;
}

.btn-save {
    background: linear-gradient(135deg, var(--forest) 0%, var(--forest-lt) 100%);
    color: white;
    box-shadow: 0 4px 16px rgba(26,61,43,.3);
    position: relative;
    overflow: hidden;
}

.btn-save::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.1), transparent);
    opacity: 0;
    transition: opacity var(--transition);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(26,61,43,.35);
}

.btn-save:hover::after { opacity: 1; }
.btn-save:active { transform: translateY(0); }

/* ── Decorative leaf motif ── */
.leaf-motif {
    position: absolute;
    right: 40px;
    top: 30px;
    opacity: .07;
    pointer-events: none;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .mentor-hero { padding: 24px 20px 70px; }
    .hero-content { flex-direction: column; align-items: flex-start; gap: 20px; }
    .hero-title { font-size: 26px; }
    .mentor-layout { padding: 0 14px; }
    .card-section { padding: 24px 20px; }
    .form-footer { flex-direction: column; gap: 16px; padding: 20px; }
    .grid-2, .grid-3 { grid-template-columns: 1fr; }
    .col-span-2, .col-span-3 { grid-column: span 1; }
    .status-grid { grid-template-columns: 1fr 1fr; }
    .avail-grid  { grid-template-columns: 1fr; }
    .photo-zone  { flex-direction: column; text-align: center; }
}
</style>
@endpush

@section('content')
<div class="mentor-page">

    {{-- ── Hero Banner ── --}}
    <div class="mentor-hero">
        {{-- Decorative SVG leaf --}}
        <svg class="leaf-motif" width="200" height="260" viewBox="0 0 200 260" fill="none">
            <path d="M100 10 C160 30, 200 100, 180 180 C160 240, 80 260, 40 220 C0 180, 20 80, 100 10Z" fill="white"/>
            <path d="M100 10 L100 240" stroke="white" stroke-width="2"/>
            <path d="M100 80 L150 50" stroke="white" stroke-width="1.5"/>
            <path d="M100 120 L160 100" stroke="white" stroke-width="1.5"/>
            <path d="M100 160 L145 150" stroke="white" stroke-width="1.5"/>
        </svg>

        <div style="max-width:1060px; margin:0 auto; padding:0 24px; position:relative; z-index:1;">
            <div class="hero-breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">›</span>
                <a href="{{ route('admin.mentors.index') }}">Mentors</a>
                <span class="sep">›</span>
                <span class="current">Add New</span>
            </div>

            <div class="hero-badge">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Tithandizane Women Hub
            </div>

            <div class="hero-content">
                <div>
                    <h1 class="hero-title">Add a New <em>Mentor</em></h1>
                    <p class="hero-subtitle">Onboard a mentor who will guide and empower women on their journey to growth and opportunity.</p>
                </div>
                <a href="{{ route('admin.mentors.index') }}" class="btn-back">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to Mentors
                </a>
            </div>
        </div>
    </div>

    {{-- ── Layout ── --}}
    <div class="mentor-layout">

        {{-- Errors --}}
        @if ($errors->any())
        <div class="alert-errors">
            <div class="alert-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
            </div>
            <div>
                <h6>Please fix the following before saving:</h6>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data" id="mentorForm">
        @csrf

        {{-- ════ CARD 1: Personal Information ════ --}}
        <div class="form-card">
            <div class="card-header-strip"></div>

            <div class="card-section">
                <div class="section-label">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    </div>
                    <div class="section-label-text">
                        <h3>Personal Information</h3>
                        <p>Basic details and contact information</p>
                    </div>
                </div>

                {{-- Photo Upload --}}
                <div class="photo-zone" onclick="document.getElementById('profile_photo').click()">
                    <div class="photo-avatar" id="photoAvatar">
                        <img id="photoPreview" src="" alt="">
                        <span class="initials" id="photoInitials">
                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                        </span>
                    </div>
                    <div class="photo-info">
                        <h4>Mentor Profile Photo</h4>
                        <p>Upload a clear, professional portrait. Recommended 400×400px or larger.<br>Accepted: JPG, PNG, GIF — max 2MB.</p>
                        <label for="profile_photo" class="btn-choose-photo" onclick="event.stopPropagation()">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                            Choose Photo
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none" onchange="handlePhoto(event)">
                    </div>
                </div>

                <div class="form-grid grid-2">
                    <div class="field">
                        <label>Full Name <span class="req">*</span></label>
                        <div class="field-wrap has-icon">
                            <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                            <input type="text" name="name" id="mentor_name"
                                   class="fc @error('name') is-invalid @enderror"
                                   placeholder="e.g. Amara Phiri"
                                   value="{{ old('name') }}"
                                   oninput="updateInitials()" required>
                        </div>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label>Email Address <span class="req">*</span></label>
                        <div class="field-wrap has-icon">
                            <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" name="email"
                                   class="fc @error('email') is-invalid @enderror"
                                   placeholder="mentor@example.com"
                                   value="{{ old('email') }}" required>
                        </div>
                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label>Phone Number <span class="opt">(optional)</span></label>
                        <div class="field-wrap has-icon">
                            <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 010 1.18 2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"/></svg>
                            <input type="text" name="phone"
                                   class="fc @error('phone') is-invalid @enderror"
                                   placeholder="+265 99 000 0000"
                                   value="{{ old('phone') }}">
                        </div>
                        @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label>Qualification <span class="opt">(optional)</span></label>
                        <div class="field-wrap has-icon">
                            <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            <input type="text" name="qualification"
                                   class="fc @error('qualification') is-invalid @enderror"
                                   placeholder="e.g. PhD Psychology, MA Counseling"
                                   value="{{ old('qualification') }}">
                        </div>
                        @error('qualification')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="field col-span-2">
                        <label>Bio / Description</label>
                        <textarea name="bio"
                                  class="fc @error('bio') is-invalid @enderror"
                                  placeholder="Write a compelling bio that introduces this mentor to women seeking guidance…">{{ old('bio') }}</textarea>
                        <span class="field-hint">Appears publicly on the mentor's profile. Min. 50 characters recommended.</span>
                        @error('bio')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- ════ SECTION 2: Expertise ════ --}}
            <div class="card-section">
                <div class="section-label">
                    <div class="section-icon" style="background: linear-gradient(135deg, #7c4a00, var(--gold));">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div class="section-label-text">
                        <h3>Expertise & Experience</h3>
                        <p>Areas of knowledge and professional background</p>
                    </div>
                </div>

                <div class="form-grid grid-2">
                    <div class="field">
                        <label>Area of Expertise <span class="req">*</span></label>
                        <div class="form-grid grid-2" style="gap: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="expertise[]" value="menstrual_hygiene" 
                                       {{ in_array('menstrual_hygiene', old('expertise', [])) ? 'checked' : '' }}
                                       class="form-check-input">
                                <span style="font-size: 13px; font-weight: 500;">Menstrual Hygiene</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="expertise[]" value="general_issues" 
                                       {{ in_array('general_issues', old('expertise', [])) ? 'checked' : '' }}
                                       class="form-check-input">
                                <span style="font-size: 13px; font-weight: 500;">General Issues</span>
                            </label>
                        </div>
                        <span class="field-hint">Select all areas of expertise</span>
                        @error('expertise')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label>Years of Experience <span class="opt">(optional)</span></label>
                        <div class="field-wrap has-icon">
                            <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <input type="number" name="experience_years"
                                   class="fc @error('experience_years') is-invalid @enderror"
                                   placeholder="e.g. 8"
                                   min="0" max="50"
                                   value="{{ old('experience_years') }}">
                        </div>
                        @error('experience_years')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- ════ SECTION 3: Availability ════ --}}
            <div class="card-section">
                <div class="section-label">
                    <div class="section-icon" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="section-label-text">
                        <h3>Availability</h3>
                        <p>Current scheduling status for new mentees</p>
                    </div>
                </div>

                <div class="avail-grid">
                    <div class="avail-card">
                        <input type="radio" name="availability" id="avail_available" value="available"
                               {{ old('availability', 'available') == 'available' ? 'checked' : '' }}>
                        <label for="avail_available">
                            <div class="avail-icon" style="background:#dcfce7;">
                                <span>🟢</span>
                            </div>
                            <span>Available</span>
                        </label>
                    </div>
                    <div class="avail-card">
                        <input type="radio" name="availability" id="avail_busy" value="busy"
                               {{ old('availability') == 'busy' ? 'checked' : '' }}>
                        <label for="avail_busy">
                            <div class="avail-icon" style="background:#fef9c3;">
                                <span>🟡</span>
                            </div>
                            <span>Busy</span>
                        </label>
                    </div>
                    <div class="avail-card">
                        <input type="radio" name="availability" id="avail_unavailable" value="unavailable"
                               {{ old('availability') == 'unavailable' ? 'checked' : '' }}>
                        <label for="avail_unavailable">
                            <div class="avail-icon" style="background:#fee2e2;">
                                <span>🔴</span>
                            </div>
                            <span>Unavailable</span>
                        </label>
                    </div>
                </div>

                {{-- Additional Availability Schedule --}}
                <div class="form-grid grid-2" style="margin-top: 24px;">
                    <div class="field">
                        <label>Available Days <span class="req">*</span></label>
                        <div class="field-wrap">
                            <select name="available_days[]" class="fc" multiple size="4" style="height: auto;">
                                <option value="Monday" {{ in_array('Monday', old('available_days', [])) ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ in_array('Tuesday', old('available_days', [])) ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ in_array('Wednesday', old('available_days', [])) ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ in_array('Thursday', old('available_days', [])) ? 'selected' : '' }}>Thursday</option>
                                <option value="Friday" {{ in_array('Friday', old('available_days', [])) ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ in_array('Saturday', old('available_days', [])) ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ in_array('Sunday', old('available_days', [])) ? 'selected' : '' }}>Sunday</option>
                            </select>
                        </div>
                        <span class="field-hint">Hold Ctrl/Cmd to select multiple days</span>
                        @error('available_days')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label>Working Hours <span class="req">*</span></label>
                        <div class="form-grid grid-2" style="gap: 12px;">
                            <div class="field-wrap has-icon">
                                <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <input type="time" name="available_time_from"
                                       class="fc @error('available_time_from') is-invalid @enderror"
                                       value="{{ old('available_time_from', '09:00') }}" required>
                            </div>
                            <div class="field-wrap has-icon">
                                <svg class="field-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <input type="time" name="available_time_to"
                                       class="fc @error('available_time_to') is-invalid @enderror"
                                       value="{{ old('available_time_to', '17:00') }}" required>
                            </div>
                        </div>
                        <span class="field-hint">From - To</span>
                        @error('available_time_from')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        @error('available_time_to')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- ════ SECTION 4: Account Status ════ --}}
            <div class="card-section">
                <div class="section-label">
                    <div class="section-icon" style="background: linear-gradient(135deg, #6d28d9, #8b5cf6);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="section-label-text">
                        <h3>Account Status</h3>
                        <p>Set the initial approval and visibility state</p>
                    </div>
                </div>

                <div class="status-grid">
                    <div class="status-card">
                        <input type="radio" name="status" id="status_active" value="active"
                               {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                        <label for="status_active">
                            <div class="status-dot dot-active"></div>
                            <span class="s-label">Active</span>
                            <span class="s-hint">Immediately visible and bookable</span>
                        </label>
                    </div>
                    <div class="status-card">
                        <input type="radio" name="status" id="status_inactive" value="inactive"
                               {{ old('status') == 'inactive' ? 'checked' : '' }}>
                        <label for="status_inactive">
                            <div class="status-dot dot-rejected"></div>
                            <span class="s-label">Inactive</span>
                            <span class="s-hint">Not visible to mentees</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ════ Footer ════ --}}
            <div class="form-footer">
                <div class="footer-note">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    Fields marked <strong style="color:var(--gold); margin:0 3px;">*</strong> are required
                </div>
                <div class="footer-actions">
                    <button type="reset" class="btn btn-reset" onclick="resetForm()">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 4v6h6M23 20v-6h-6"/><path d="M20.49 9A9 9 0 005.64 5.64L1 10M23 14l-4.64 4.36A9 9 0 013.51 15"/></svg>
                        Reset Form
                    </button>
                    <button type="submit" class="btn btn-save">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Mentor
                    </button>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>

<script>
function handlePhoto(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const img = document.getElementById('photoPreview');
        img.src = ev.target.result;
        img.style.display = 'block';
        document.getElementById('photoInitials').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function updateInitials() {
    const name = document.getElementById('mentor_name').value.trim();
    const el   = document.getElementById('photoInitials');
    const img  = document.getElementById('photoPreview');
    if (img.style.display === 'block') return;
    if (name) {
        const parts = name.split(' ');
        const initials = parts.length >= 2
            ? parts[0][0].toUpperCase() + parts[parts.length-1][0].toUpperCase()
            : parts[0][0].toUpperCase();
        el.textContent = initials;
        el.style.fontFamily = "'Playfair Display', serif";
        el.style.fontSize   = "28px";
    } else {
        el.innerHTML = `<svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>`;
    }
}

function resetForm() {
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('photoInitials').innerHTML = `<svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>`;
}
</script>
@endsection