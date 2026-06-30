@extends('admin.layouts.admin')

@section('page-title', 'Guidance Content Details')
@section('page-subtitle', 'View guidance content details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.guidance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition hover:opacity-90" style="background: var(--light-purple); color: var(--purple);">
        <i class="fas fa-arrow-left"></i> Back to Content
    </a>

    <!-- Content Details -->
    <div class="card p-6">
        <div class="flex gap-6">
            <!-- Photo -->
            @if($content->photo)
                <div class="flex-shrink-0">
                    <img src="{{ $content->photo_url }}" alt="{{ $content->title }}" class="w-48 h-48 rounded-xl object-cover">
                </div>
            @endif
            
            <!-- Info -->
            <div class="flex-1">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">{{ $content->title }}</h1>
                        <div class="flex items-center gap-3">
                            @if($content->status === 'published')
                                <span class="status-badge status-active">Published</span>
                            @else
                                <span class="status-badge status-inactive">Unpublished</span>
                            @endif
                            
                            @if($content->category === 'general')
                                <span class="badge badge-purple">General</span>
                            @else
                                <span class="badge" style="background: #fce7f3; color: #ec4899;">Menstrual Hygiene</span>
                            @endif
                            
                            <span class="badge badge-info">{{ ucfirst($content->language ?? 'en') }}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        @if($content->status === 'unpublished')
                            <button onclick="publishContent({{ $content->id }})" class="px-4 py-2 rounded-lg font-medium transition hover:opacity-90" style="background: var(--light-teal); color: var(--teal-green);">
                                <i class="fas fa-check mr-1"></i> Publish
                            </button>
                        @else
                            <button onclick="unpublishContent({{ $content->id }})" class="px-4 py-2 rounded-lg font-medium transition hover:opacity-90" style="background: var(--light-orange); color: var(--orange);">
                                <i class="fas fa-times mr-1"></i> Unpublish
                            </button>
                        @endif
                        
                        <button onclick="deleteContent({{ $content->id }}, '{{ Str::limit($content->title, 30) }}')" class="px-4 py-2 rounded-lg font-medium transition hover:opacity-90" style="background: var(--light-red); color: var(--red);">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>
                
                <!-- Mentor Info -->
                <div class="flex items-center gap-3 mb-4 p-3 rounded-lg" style="background: var(--bg-primary);">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: var(--light-purple);">
                        <i class="fas fa-user" style="color: var(--purple);"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $content->mentor->name ?? 'Unknown Mentor' }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $content->mentor->email ?? '' }}</p>
                    </div>
                </div>
                
                <!-- Created Date -->
                <p class="text-sm" style="color: var(--text-secondary);">
                    <i class="fas fa-calendar-alt mr-2"></i>Created: {{ $content->created_at->format('F d, Y \a\t H:i') }}
                </p>
            </div>
        </div>
        
        <!-- Content Body -->
        <div class="mt-6 pt-6" style="border-top: 1px solid var(--border-color);">
            <h3 class="text-lg font-semibold mb-3" style="color: var(--text-primary);">Content</h3>
            <div class="prose max-w-none" style="color: var(--text-primary);">
                {!! nl2br(e($content->body)) !!}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-container max-w-md">
        <div class="modal-header" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
            <h3><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Deletion</h3>
            <button onclick="closeModal('deleteModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-4">
                <i class="fas fa-trash-alt text-5xl" style="color: #dc2626;"></i>
            </div>
            <p class="text-primary-color mb-4 text-center">
                Are you sure you want to delete <strong id="deleteContentTitle" class="text-red"></strong>?
            </p>
            <p class="text-sm text-secondary-color text-center">
                This action cannot be undone. All associated data will be permanently removed.
            </p>
        </div>
        <div class="modal-footer justify-center">
            <button onclick="closeModal('deleteModal')" class="px-6 py-2.5 rounded-lg border transition hover:bg-gray-100" style="border-color: var(--border-color); color: var(--text-secondary);">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <button onclick="confirmDelete()" class="px-6 py-2.5 rounded-lg transition hover:opacity-90" style="background: #dc2626; color: white;">
                <i class="fas fa-trash-alt mr-2"></i>Delete
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteContentId = null;
let deleteContentTitle = '';

function publishContent(id) {
    fetch(`/admin/guidance/${id}/publish`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Failed to publish content', 'error');
        }
    })
    .catch(() => showNotification('Failed to publish content', 'error'));
}

function unpublishContent(id) {
    fetch(`/admin/guidance/${id}/unpublish`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Failed to unpublish content', 'error');
        }
    })
    .catch(() => showNotification('Failed to unpublish content', 'error'));
}

function deleteContent(id, title) {
    deleteContentId = id;
    deleteContentTitle = title;
    document.getElementById('deleteContentTitle').textContent = title;
    document.getElementById('deleteModal').classList.add('show');
}

function confirmDelete() {
    fetch(`/admin/guidance/${deleteContentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeModal('deleteModal');
            setTimeout(() => location.href = '{{ route('admin.guidance.index') }}', 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(err => showNotification('Failed to delete content', 'error'));
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `flash flash-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}" style="font-size:18px;flex-shrink:0;"></i>
        <span style="font-size:14px;font-weight:500;flex:1;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;opacity:.6;">
            <i class="fas fa-times"></i>
        </button>
    `;
    const pageContent = document.querySelector('.page-content .page-inner');
    if (pageContent) {
        pageContent.insertBefore(notification, pageContent.firstChild);
    }
    setTimeout(() => notification.remove(), 5000);
}
</script>
@endpush
