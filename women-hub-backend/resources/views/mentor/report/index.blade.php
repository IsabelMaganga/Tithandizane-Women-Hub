@extends('mentor.layouts.dashboard')
@section('title')
    Report Issue
@endsection

@push('styles')
    <style>
        /* Modern Report Form Styling */
        .report-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .form-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.02);
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 28px;
            color: white;
        }

        .form-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .form-header p {
            font-size: 13px;
            opacity: 0.85;
            margin: 4px 0 0 0;
        }

        .form-body {
            padding: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 6px;
        }

        .form-label .optional {
            font-weight: 400;
            color: #a0aec0;
            font-size: 12px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .form-control.error {
            border-color: #fc8181;
            background: #fff5f5;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .form-error {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            font-size: 13px;
            color: #e53e3e;
            animation: slideDown 0.3s ease;
        }

        .form-error i {
            font-size: 14px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.35);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Right Section - Info Card */
        .info-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            height: 100%;
        }

        .info-card .illustration {
            text-align: center;
            margin-bottom: 24px;
        }

        .info-card .illustration img {
            max-width: 200px;
            height: auto;
        }

        .info-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card h3 i {
            color: #667eea;
        }

        .rules-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .rules-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f7fafc;
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
        }

        .rules-list li:last-child {
            border-bottom: none;
        }

        .rules-list li i {
            color: #48bb78;
            font-size: 16px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .rules-list li .rule-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Footer Actions */
        .footer-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            margin-top: 16px;
        }

        .footer-actions .left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-actions .left a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .footer-actions .left a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        .footer-actions .left i {
            font-size: 14px;
        }

        .footer-actions .right {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-actions .right a {
            color: #718096;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-actions .right a:hover {
            color: #667eea;
        }

        .footer-actions .right .email {
            color: #2d3748;
            font-weight: 500;
        }

        .footer-actions .right .email i {
            color: #667eea;
        }

        /* Character Counter */
        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #a0aec0;
            margin-top: 4px;
        }

        .char-counter.warning {
            color: #ecc94b;
        }

        .char-counter.danger {
            color: #fc8181;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .grid-cols-2 {
                grid-template-columns: 1fr !important;
            }

            .right-section {
                order: -1;
            }

            .info-card .illustration img {
                max-width: 150px;
            }
        }

        @media (max-width: 640px) {
            .report-container {
                padding: 1rem;
            }

            .form-body {
                padding: 20px;
            }

            .form-header {
                padding: 16px 20px;
            }

            .form-header h2 {
                font-size: 18px;
            }

            .footer-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .footer-actions .right {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="report-container">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                     Report an Issue
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Help us improve by reporting bugs, sharing feedback, or requesting features
                </p>
            </div>
            <div class="hidden md:block">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-full">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Active Support
                </span>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Section (2/3 width) -->
            <div class="lg:col-span-2">
                <div class="form-card">
                    <div class="form-header">
                        <h2>
                            <i class="fas fa-pen"></i> Submit Your Report
                        </h2>
                        <p>Fill in the details below and we'll get back to you within 24 hours</p>
                    </div>

                    <div class="form-body">
                        <form method="POST" action="{{ route('mentor.submit.report') }}">
                            @csrf

                            <!-- Username -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user text-gray-400"></i> Username
                                    <span class="optional">(optional)</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') error @enderror"
                                       placeholder="Enter your username or leave blank">
                                @error('name')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Issue Title -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-heading text-gray-400"></i> Issue Title
                                    <span class="optional">(min 15 characters)</span>
                                </label>
                                <input type="text"
                                       name="title"
                                       value="{{ old('title') }}"
                                       class="form-control @error('title') error @enderror"
                                       placeholder="e.g., Login page not loading properly">
                                @error('title')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Issue Type -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag text-gray-400"></i> Issue Type
                                </label>
                                <select name="type" class="form-control @error('type') error @enderror">
                                    <option value="null" {{ old('type') == 'null' ? 'selected' : '' }}>
                                        Select issue type...
                                    </option>
                                    <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>
                                         Bug / Error
                                    </option>
                                    <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>
                                         Feedback / Suggestion
                                    </option>
                                    <option value="request" {{ old('type') == 'request' ? 'selected' : '' }}>
                                         Feature Request
                                    </option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>
                                         Other
                                    </option>
                                </select>
                                @error('type')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Issue Date -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar text-gray-400"></i> When did this occur?
                                </label>
                                <input type="datetime-local"
                                       name="issue_date"
                                       value="{{ old('issue_date') }}"
                                       class="form-control @error('issue_date') error @enderror">
                                @error('issue_date')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-align-left text-gray-400"></i> Description
                                    <span class="optional">(min 200 characters)</span>
                                </label>
                                <textarea name="description"
                                          class="form-control @error('description') error @enderror"
                                          rows="8"
                                          placeholder="Please provide a detailed description of the issue...">{{ old('description') }}</textarea>
                                <div class="char-counter" id="charCounter">0 / 200 minimum</div>
                                @error('description')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Submit -->
                            <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-shield-alt text-green-500"></i>
                                    Your report is confidential and secure
                                </div>
                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-paper-plane"></i>
                                    Submit Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (1/3 width) -->
            <div class="lg:col-span-1">
                <div class="info-card">
                    <div class="illustration">
                        <img src="{{ asset('/loginpng.png') }}" alt="Report illustration" class="mx-auto">
                    </div>

                    <h3>
                        <i class="fas fa-list-check"></i> Guidelines
                    </h3>

                    <ul class="rules-list">
                        <li>
                            <span class="rule-number">1</span>
                            <span><strong>Clear Title</strong> — Use a concise, descriptive title for your issue</span>
                        </li>
                        <li>
                            <span class="rule-number">2</span>
                            <span><strong>Detailed Description</strong> — Provide step-by-step details to help us reproduce the issue</span>
                        </li>
                        <li>
                            <span class="rule-number">3</span>
                            <span><strong>Attach Screenshots</strong> — If possible, include screenshots or screen recordings</span>
                        </li>
                        <li>
                            <span class="rule-number">4</span>
                            <span><strong>Be Specific</strong> — Mention device, browser, and any error messages you received</span>
                        </li>
                        <li>
                            <span class="rule-number">5</span>
                            <span><strong>Stay Professional</strong> — Use respectful language when describing the issue</span>
                        </li>
                    </ul>

                    <!-- Quick Stats -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-blue-600">24</div>
                                <div class="text-xs text-gray-500">Avg. Response Time</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">98%</div>
                                <div class="text-xs text-gray-500">Satisfaction Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="footer-actions">
            <div class="left">
                <i class="fas fa-clock text-gray-400"></i>
                <a href="{{ route('mentor.pending.reports') }}">
                    View Pending Reports
                </a>
                <span class="text-gray-300">|</span>
                <i class="fas fa-history text-gray-400"></i>
                <a href="#">My Reports</a>
            </div>

            <div class="right">
                <a href="#">
                    <i class="fas fa-life-ring"></i> Help Centre
                </a>
                <a href="#">
                    <i class="fas fa-comment-dots"></i> Live Chat
                </a>
                <a href="mailto:tithandizane@org.com" class="email">
                    <i class="fas fa-envelope"></i> tithandizane@org.com
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide error messages after 5 seconds
            const errorMessages = document.querySelectorAll('.form-error');
            errorMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.display = 'none';
                }, 5000);
            });

            // Character counter for description
            const description = document.querySelector('textarea[name="description"]');
            const counter = document.getElementById('charCounter');

            if (description && counter) {
                description.addEventListener('input', function() {
                    const length = this.value.length;
                    const minLength = 200;

                    counter.textContent = `${length} / ${minLength} minimum`;

                    // Update styling based on character count
                    counter.className = 'char-counter';
                    if (length > 0 && length < minLength) {
                        counter.classList.add('warning');
                    } else if (length >= minLength) {
                        counter.classList.add('text-green-600');
                    } else {
                        counter.classList.remove('warning', 'text-green-600');
                    }
                });

                // Trigger initial count
                description.dispatchEvent(new Event('input'));
            }

            // Auto-close any global error alerts after 5 seconds
            const globalErrors = document.querySelectorAll('.alert-danger, .alert-error');
            globalErrors.forEach(err => {
                setTimeout(() => {
                    err.style.display = 'none';
                }, 5000);
            });

            // Optional: Add smooth form submission feedback
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                        submitBtn.disabled = true;
                    }
                });
            }
        });
    </script>
@endpush
