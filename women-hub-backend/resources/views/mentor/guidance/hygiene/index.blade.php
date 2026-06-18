@extends('mentor.layouts.dashboard')

@section('title') Hygiene Resources @endsection

@push('styles')
    <style>
        /* Modern Hygiene Content Styling */
        .hygiene-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .hygiene-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        .hygiene-card .image-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            flex-shrink: 0;
        }

        .hygiene-card .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hygiene-card:hover .image-wrapper img {
            transform: scale(1.08);
        }

        .hygiene-card .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .hygiene-card:hover .image-overlay {
            opacity: 1;
        }

        .hygiene-card .badge-container {
            position: absolute;
            top: 14px;
            left: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .hygiene-card .badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        .badge-category {
            background: rgba(102, 126, 234, 0.9);
            color: white;
        }

        .badge-status {
            position: absolute;
            top: 14px;
            right: 14px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        .badge-status.published {
            background: rgba(72, 187, 120, 0.9);
            color: white;
        }

        .badge-status.draft {
            background: rgba(237, 137, 54, 0.9);
            color: white;
        }

        .badge-status .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse-dot 2s infinite;
        }

        .badge-status.published .dot {
            background: #48bb78;
        }

        .badge-status.draft .dot {
            background: #ed8936;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .hygiene-card .content {
            padding: 20px 22px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .hygiene-card .title {
            font-size: 17px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
            line-height: 1.4;
            transition: color 0.3s ease;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hygiene-card:hover .title {
            color: #667eea;
        }

        .hygiene-card .description {
            font-size: 14px;
            color: #718096;
            line-height: 1.6;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .hygiene-card .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid #f7fafc;
            margin-top: auto;
        }

        .hygiene-card .date-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #a0aec0;
        }

        .hygiene-card .date-info i {
            font-size: 13px;
        }

        .hygiene-card .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #667eea;
            background: rgba(102, 126, 234, 0.08);
            border-radius: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 1.5px solid transparent;
        }

        .hygiene-card .view-btn:hover {
            background: #667eea;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .hygiene-card .view-btn i {
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .hygiene-card .view-btn:hover i {
            transform: translateX(3px);
        }

        /* Animation for cards */
        .hygiene-card {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .hygiene-card:nth-child(1) { animation-delay: 0.05s; }
        .hygiene-card:nth-child(2) { animation-delay: 0.1s; }
        .hygiene-card:nth-child(3) { animation-delay: 0.15s; }
        .hygiene-card:nth-child(4) { animation-delay: 0.2s; }
        .hygiene-card:nth-child(5) { animation-delay: 0.25s; }
        .hygiene-card:nth-child(6) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .hygiene-card .image-wrapper {
                height: 160px;
            }

            .hygiene-card .title {
                font-size: 15px;
            }

            .hygiene-card .description {
                font-size: 13px;
                -webkit-line-clamp: 2;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl px-4 py-6 mx-auto sm:px-6 lg:px-8">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
            <a href="{{ route('mentor.Guidance') }}" class="flex items-center gap-1.5 text-gray-500 hover:text-purple-600 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Guidance</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-700 font-medium">Hygiene</span>
        </nav>

        <!-- Header Section -->
        <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-600 p-8 md:p-10">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-10 -mb-10"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="text-2xl text-white fas fa-hand-sparkles"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white md:text-4xl">Hygiene Resources</h1>
                    </div>
                </div>
                <p class="max-w-2xl text-purple-100 text-sm md:text-base leading-relaxed">
                    Explore our comprehensive collection of hygiene resources designed to promote health, wellness, and cleanliness in your community. Stay informed with expert guidelines and practical tips.
                </p>

                <!-- Stats -->
                <div class="flex flex-wrap gap-6 mt-6">
                    <div class="flex items-center gap-2 text-white/90">
                        <i class="fas fa-file-alt text-purple-200"></i>
                        <span class="text-sm">{{ $hygiene->count() }} Resources</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <i class="fas fa-calendar-check text-purple-200"></i>
                        <span class="text-sm">Last updated: {{ now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Grid -->
        @if($hygiene->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($hygiene as $item)
                    <div class="hygiene-card">
                        <!-- Image Section -->
                        <div class="image-wrapper">
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}"
                                     class="w-full h-full object-cover"
                                     alt="{{ $item->title }}"
                                     loading="lazy">
                            @else
                                <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-100 to-indigo-100">
                                    <img src="{{ asset('images/Ellipse 3.png') }}"
                                         class="w-28 opacity-60"
                                         alt="{{ $item->title }}">
                                </div>
                            @endif
                            <div class="image-overlay"></div>

                            <!-- Category Badge -->
                            <div class="badge-container">
                                <span class="badge badge-category">
                                    <i class="fas fa-tag text-[10px] mr-1"></i>
                                    {{ $item->category ?? 'General' }}
                                </span>
                            </div>

                            <!-- Status Badge -->
                            <span class="badge-status {{ $item->is_published ? 'published' : 'draft' }}">
                                <span class="dot"></span>
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>

                        <!-- Content Section -->
                        <div class="content">
                            <h3 class="title">{{ $item->title }}</h3>
                            <p class="description">{{ Str::limit($item->content, 120) }}</p>

                            <div class="card-footer">
                                <div class="date-info">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $hygieneCreatedAt ?? $item->created_at->format('M d, Y') }}</span>
                                </div>

                                <a href="#" class="view-btn" onclick="event.preventDefault();">
                                    Read More
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination (if needed) -->
            @if(method_exists($hygiene, 'links'))
                <div class="mt-8">
                    {{ $hygiene->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="text-4xl text-purple-500 fas fa-file-alt"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Hygiene Resources Found</h3>
                <p class="text-gray-500 text-sm max-w-sm mx-auto">
                    There are currently no hygiene resources available. Check back later for updates.
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Add click handler for "Read More" buttons
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const card = this.closest('.hygiene-card');
                    const title = card.querySelector('.title')?.textContent || 'Resource';

                    // You can implement a modal or redirect here
                    console.log(`Viewing: ${title}`);

                    // Example: Show a toast notification
                    showToast(`Opening: ${title}`);
                });
            });

            // Toast notification function (optional)
            function showToast(message) {
                const existing = document.querySelector('.custom-toast');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.className = 'custom-toast fixed bottom-6 right-6 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-lg text-sm z-50 animate-fade-in-up';
                toast.innerHTML = `<i class="fas fa-info-circle mr-2"></i> ${message}`;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Add animation styles for toast
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeInUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-fade-in-up {
                    animation: fadeInUp 0.4s ease forwards;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endpush
