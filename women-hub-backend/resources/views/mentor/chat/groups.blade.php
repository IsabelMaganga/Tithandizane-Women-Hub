@extends('mentor.layouts.dashboard')
@section('title')
    Chat Groups
@endsection

@push('styles')
    <style>
        /* Modern Chat Groups Styling */
        .group-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .group-card:hover {
            transform: translateY(-4px);
        }

        .group-card-inner {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .group-card-inner:hover {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        .group-image-wrapper {
            position: relative;
            height: 160px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .group-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .group-card-inner:hover .group-image-wrapper img {
            transform: scale(1.05);
        }

        .group-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 100%);
        }

        .group-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: #4a5568;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .group-status {
            position: absolute;
            bottom: 12px;
            left: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            padding: 4px 12px;
            border-radius: 20px;
            color: white;
            font-size: 11px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #48bb78;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .group-content {
            padding: 16px 18px 18px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .group-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .group-description {
            font-size: 13px;
            color: #718096;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
            margin-bottom: 12px;
        }

        .group-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #f7fafc;
        }

        .group-members {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #a0aec0;
        }

        .group-members i {
            font-size: 14px;
        }

        .join-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .join-btn:hover {
            transform: scale(1);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .join-btn-outline {
            background: transparent;
            color: #667eea;
            border: 1.5px solid #e2e8f0;
        }

        .join-btn-outline:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .empty-state i {
            font-size: 48px;
            color: #e2e8f0;
            margin-bottom: 16px;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            min-width: 200px;
        }

        .search-box:focus-within {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-box i {
            color: #a0aec0;
            margin-right: 10px;
            font-size: 14px;
        }

        .search-box input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            color: #2d3748;
            width: 100%;
        }

        .search-box input::placeholder {
            color: #a0aec0;
        }

        .create-group-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: #48bb78;
            color: white;
            font-size: 14px;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .create-group-btn:hover {
            background: #38a169;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .search-box {
                width: 100%;
                min-width: unset;
            }

            .create-group-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
<div class="w-full max-w-full p-4 md:p-6 mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                 Chat Groups
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Connect with communities that matter to you
            </p>
        </div>

        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search groups..." id="searchGroups">
            </div>
            <button class="create-group-btn" onclick="createGroup()">
                <i class="fas fa-plus"></i> New Group
            </button>
        </div>
    </div>

    <!-- Groups Grid -->
    @php
        $groups = [
            [
                'name' => 'Health & Wellness',
                'description' => 'A supportive space to discuss health, wellness, and self-care tips.',
                'members' => 12,
                'image' => asset('/images/background.png'),
                'active' => true,
                'category' => 'Health'
            ],
            [
                'name' => 'Career & Leadership',
                'description' => 'Empowering women to lead, grow, and succeed in their careers.',
                'members' => 8,
                'image' => asset('/images/background.png'),
                'active' => true,
                'category' => 'Career'
            ],
            [
                'name' => 'Entrepreneurship',
                'description' => 'Share business ideas, get feedback, and grow your venture.',
                'members' => 15,
                'image' => asset('/images/background.png'),
                'active' => false,
                'category' => 'Business'
            ]
        ];
    @endphp

    @if(count($groups) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 mt-10 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($groups as $group)
                <div class="group-card" data-search="{{ strtolower($group['name'] . ' ' . $group['category']) }}">
                    <div class="group-card-inner">
                        <!-- Image Section -->
                        <div class="group-image-wrapper">
                            <img src="{{ $group['image'] }}" alt="{{ $group['name'] }}" loading="lazy">
                            <div class="group-image-overlay"></div>

                            <span class="group-badge">
                                <i class="fas fa-tag"></i> {{ $group['category'] }}
                            </span>

                            <div class="group-status">
                                <span class="status-dot"></span>
                                {{ $group['active'] ? 'Active now' : 'Offline' }}
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="group-content">
                            <h3 class="group-title">{{ $group['name'] }}</h3>
                            <p class="group-description">{{ $group['description'] }}</p>

                            <div class="group-footer">
                                <span class="group-members">
                                    <i class="fas fa-users"></i>
                                    {{ $group['members'] }} members
                                </span>

                                <a href="" class="join-btn">
                                    <i class="fas fa-arrow-right"></i> Join
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No groups available</h3>
            <p class="text-gray-500 mb-4">Be the first to create a chat group!</p>
            <button class="join-btn" onclick="createGroup()">
                <i class="fas fa-plus"></i> Create Group
            </button>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchGroups')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.group-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const searchData = card.dataset.search || '';
            const matches = searchData.includes(searchTerm);
            card.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        // Show empty state if no results
        const grid = document.querySelector('.grid');
        const existingEmpty = document.querySelector('.search-empty-state');

        if (visibleCount === 0 && searchTerm !== '') {
            if (!existingEmpty) {
                const emptyMsg = document.createElement('div');
                emptyMsg.className = 'search-empty-state col-span-full text-center py-12';
                emptyMsg.innerHTML = `
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No groups found matching "<strong>${e.target.value}</strong>"</p>
                `;
                grid?.appendChild(emptyMsg);
            }
        } else {
            existingEmpty?.remove();
        }
    });

    // Create Group Function
    function createGroup() {
        // Replace with your actual route
        {{--  window.location.href = "";  --}}
    }

    // Optional: Add hover sound effect or analytics
    document.querySelectorAll('.join-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // You can add analytics tracking here
            console.log('Joined group:', this.closest('.group-title')?.textContent);
        });
    });
</script>
@endpush
