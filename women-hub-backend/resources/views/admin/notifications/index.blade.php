@extends('admin.layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', 'View and manage your notifications')

@push('styles')
<style>
    .notification-item {
        transition: all 0.2s ease;
    }
    .notification-item:hover {
        background: var(--bg-secondary);
    }
    .notification-item.unread {
        background: var(--light-blue);
        border-left: 4px solid var(--blue);
    }
    .notification-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .type-info { background: var(--light-blue); color: var(--blue); }
    .type-success { background: var(--light-green); color: var(--teal-green); }
    .type-warning { background: var(--light-orange); color: var(--orange); }
    .type-danger { background: var(--light-red); color: var(--red); }
    .type-event { background: var(--light-purple); color: var(--purple); }
</style>
@endpush

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Notifications</h1>
        <p class="text-sm" style="color: var(--text-secondary);">View and manage your notifications</p>
    </div>
    <div class="flex gap-3">
        @if(auth()->guard('admin')->user()->unreadNotifications()->count() > 0)
            <button onclick="markAllAsRead()" class="px-4 py-2 rounded-xl text-white font-medium transition hover:opacity-90" style="background: var(--blue);">
                <i class="fas fa-check-double mr-2"></i>Mark All as Read
            </button>
        @endif
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl font-medium transition hover:opacity-90" style="background: var(--light-gray); color: var(--text-primary);">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>
</div>

<div class="rounded-2xl shadow-lg" style="background: var(--card-bg); border: 1px solid var(--border-color);">
    <div class="p-6">
        @if($notifications->count() > 0)
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    <div class="notification-item {{ $notification->is_read ? '' : 'unread' }} p-4 rounded-xl border" style="border-color: var(--border-color);">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="notification-badge type-{{ $notification->type }}">{{ $notification->type }}</span>
                                    <span class="text-xs" style="color: var(--text-secondary);">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold mb-1" style="color: var(--text-primary);">
                                    {{ $notification->title }}
                                </h3>
                                <p class="text-sm" style="color: var(--text-secondary);">
                                    {{ $notification->message }}
                                </p>
                                @if($notification->data)
                                    @php
                                        $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                                    @endphp
                                    @if(isset($data['link']))
                                        <a href="{{ $data['link'] }}" class="inline-block mt-2 text-sm font-medium" style="color: var(--purple);">
                                            View Details <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                            <div class="flex gap-2 ml-4">
                                @if(!$notification->is_read)
                                    <button onclick="markAsRead({{ $notification->id }})" class="p-2 rounded-lg transition hover:opacity-80" style="background: var(--light-blue); color: var(--blue);" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <button onclick="deleteNotification({{ $notification->id }})" class="p-2 rounded-lg transition hover:opacity-80" style="background: var(--light-red); color: var(--red);" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-6xl mb-4" style="color: var(--text-muted);"></i>
                <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">No notifications</h3>
                <p class="text-sm" style="color: var(--text-secondary);">You're all caught up!</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function markAsRead(notificationId) {
        fetch(`/admin/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function markAllAsRead() {
        if (confirm('Mark all notifications as read?')) {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }

    function deleteNotification(notificationId) {
        if (confirm('Delete this notification?')) {
            fetch(`/admin/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endpush
