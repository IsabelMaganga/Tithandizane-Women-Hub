@extends('admin.layouts.admin')

@section('page-title', 'Guidance Content Management')
@section('page-subtitle', 'View and manage all guidance content from mentors')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card p-4 stat-card-teal">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm" style="color: var(--text-secondary);">Total Content</p>
                    <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $totalContent }}</p>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: var(--light-teal);">
                    <i class="fas fa-book" style="color: var(--teal-green); font-size: 20px;"></i>
                </div>
            </div>
        </div>
        
        <div class="card p-4 stat-card-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm" style="color: var(--text-secondary);">Published</p>
                    <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $publishedContent }}</p>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: var(--light-blue);">
                    <i class="fas fa-check-circle" style="color: var(--blue); font-size: 20px;"></i>
                </div>
            </div>
        </div>
        
        <div class="card p-4 stat-card-orange">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm" style="color: var(--text-secondary);">Unpublished</p>
                    <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $unpublishedContent }}</p>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: var(--light-orange);">
                    <i class="fas fa-clock" style="color: var(--orange); font-size: 20px;"></i>
                </div>
            </div>
        </div>
        
        <div class="card p-4 stat-card-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm" style="color: var(--text-secondary);">General</p>
                    <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $generalContent }}</p>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: var(--light-purple);">
                    <i class="fas fa-info-circle" style="color: var(--purple); font-size: 20px;"></i>
                </div>
            </div>
        </div>
        
        <div class="card p-4" style="border-left: 4px solid #ec4899;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm" style="color: var(--text-secondary);">Menstrual Hygiene</p>
                    <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $hygieneContent }}</p>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: #fce7f3;">
                    <i class="fas fa-heart" style="color: #ec4899; font-size: 20px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or content..." 
                       class="w-full px-3 py-2 rounded-lg border" style="border-color: var(--border-color);">
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Category</label>
                <select name="category" class="w-full px-3 py-2 rounded-lg border" style="border-color: var(--border-color);">
                    <option value="">All Categories</option>
                    <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="menstrual_hygiene" {{ request('category') == 'menstrual_hygiene' ? 'selected' : '' }}>Menstrual Hygiene</option>
                </select>
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border" style="border-color: var(--border-color);">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="unpublished" {{ request('status') == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                </select>
            </div>
            
            <button type="submit" class="px-4 py-2 rounded-lg font-medium transition hover:opacity-90" style="background: var(--purple); color: white;">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            
            <a href="{{ route('admin.guidance.index') }}" class="px-4 py-2 rounded-lg font-medium border transition hover:bg-gray-100" style="border-color: var(--border-color); color: var(--text-secondary);">
                <i class="fas fa-times mr-1"></i> Clear
            </a>
        </form>
    </div>

    <!-- Content Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="background: var(--bg-primary); border-bottom: 1px solid var(--border-color);">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Mentor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Language</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guidance as $item)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($item->photo)
                                    <img src="{{ $item->photo_url }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--light-purple);">
                                        <i class="fas fa-file-alt" style="color: var(--purple);"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium" style="color: var(--text-primary);">{{ Str::limit($item->title, 40) }}</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ Str::limit($item->body, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm" style="color: var(--text-primary);">{{ $item->mentor->name ?? 'Unknown' }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">{{ $item->mentor->email ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($item->category === 'general')
                                <span class="badge badge-purple">General</span>
                            @else
                                <span class="badge" style="background: #fce7f3; color: #ec4899;">Menstrual Hygiene</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($item->status === 'published')
                                <span class="status-badge status-active">Published</span>
                            @else
                                <span class="status-badge status-inactive">Unpublished</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm" style="color: var(--text-primary);">{{ ucfirst($item->language ?? 'en') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm" style="color: var(--text-primary);">{{ $item->created_at->format('M d, Y') }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">{{ $item->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.guidance.show', $item->id) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition hover:opacity-90" style="background: var(--light-blue); color: var(--blue);">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                
                                @if($item->status === 'unpublished')
                                    <button onclick="publishContent({{ $item->id }})" class="px-3 py-1.5 rounded-lg text-sm font-medium transition hover:opacity-90" style="background: var(--light-teal); color: var(--teal-green);">
                                        <i class="fas fa-check mr-1"></i> Publish
                                    </button>
                                @else
                                    <button onclick="unpublishContent({{ $item->id }})" class="px-3 py-1.5 rounded-lg text-sm font-medium transition hover:opacity-90" style="background: var(--light-orange); color: var(--orange);">
                                        <i class="fas fa-times mr-1"></i> Unpublish
                                    </button>
                                @endif
                                
                                <button onclick="deleteContent({{ $item->id }}, '{{ Str::limit($item->title, 30) }}')" class="px-3 py-1.5 rounded-lg text-sm font-medium transition hover:opacity-90" style="background: var(--light-red); color: var(--red);">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-4xl mb-3" style="color: var(--text-secondary); opacity: 0.3;"></i>
                            <p class="text-sm" style="color: var(--text-secondary);">No guidance content found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($guidance->hasPages())
        <div class="px-4 py-3 border-t" style="border-color: var(--border-color);">
            {{ $guidance->appends(request()->query())->links() }}
        </div>
        @endif
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
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(err => showNotification('Failed to publish content', 'error'));
}

function unpublishContent(id) {
    fetch(`/admin/guidance/${id}/unpublish`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(err => showNotification('Failed to unpublish content', 'error'));
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
            setTimeout(() => location.reload(), 1500);
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
