@extends('admin.layouts.admin')

@section('title', 'Backup')
@section('page-title', 'Database Backup')
@section('page-subtitle', 'Manage database backups and restore operations.')

@section('content')
<div class="px-4 py-0 mx-auto max-w-4xl sm:px-6 lg:px-8">

   <div class="mb-6">
    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition hover:opacity-80" style="background: #8b5cf6; color: white;">
        <i class="fas fa-arrow-left"></i> Back to Settings
    </a>
</div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" style="background: var(--card-bg); border-color: var(--border-color);">
        <!-- Backup Info -->
        <div class="p-4 rounded-lg mb-6" style="background: var(--light-teal);">
            <p class="font-semibold" style="color: var(--text-primary);">
                <i class="fas fa-check-circle text-green-600 mr-2"></i> 
                Last Backup: {{ $lastBackup ?? 'No backups found' }}
            </p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">
                Backup size: {{ $backupSize ?? '0 B' }}
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-4">
            <form action="{{ route('admin.settings.backup.run') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-4 py-3 rounded-lg text-sm font-semibold transition hover:opacity-90" style="background: #8b5cf6; color: white;">
                    <i class="fas fa-play mr-2"></i> Run Manual Backup
                </button>
            </form>
            
            @if(isset($backups) && count($backups) > 0)
                <a href="{{ route('admin.settings.backup.download', ['file' => $backups[0]->name]) }}" class="w-full px-4 py-3 rounded-lg text-sm font-semibold text-center transition hover:opacity-90" style="background: var(--teal-green); color: white; text-decoration: none; display: block;">
                    <i class="fas fa-download mr-2"></i> Download Latest Backup
                </a>
            @else
                <button class="w-full px-4 py-3 rounded-lg text-sm font-semibold text-center cursor-not-allowed opacity-60" style="background: var(--light-gray); color: var(--text-secondary);">
                    <i class="fas fa-download mr-2"></i> No Backup Available
                </button>
            @endif
        </div>
        
        <!-- Backup History -->
        @if(isset($backups) && count($backups) > 0)
        <div class="mt-6 pt-4 border-t" style="border-color: var(--border-color);">
            <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Backup History</h4>
            <div class="space-y-2">
                @foreach($backups as $backup)
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition" style="background: var(--card-bg);">
                        <div>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $backup->name }}</span>
                            <span class="text-xs ml-3" style="color: var(--text-secondary);">{{ $backup->date }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs" style="color: var(--text-secondary);">{{ $backup->size }}</span>
                            <a href="{{ route('admin.settings.backup.download', ['file' => $backup->name]) }}" class="text-sm" style="color: var(--blue);">
                                <i class="fas fa-download"></i>
                            </a>
                            <form action="{{ route('admin.settings.backup.delete', ['file' => $backup->name]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm" style="color: var(--red);">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
    </div>
</div>
@endsection