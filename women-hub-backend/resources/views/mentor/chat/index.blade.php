@extends('mentor.layouts.dashboard')
@section('title') Private Chats @endsection

@push('styles')
<style>
    .chat-row {
        display: flex; align-items: center; gap: 14px;
        background: white; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 16px; margin-bottom: 12px; text-decoration: none;
        transition: border-color .15s, box-shadow .15s, transform .15s;
    }
    .chat-row:hover { border-color: #c4b5fd; box-shadow: 0 8px 24px rgba(124,58,237,.08); transform: translateY(-1px); }
    .avatar {
        width: 44px; height: 44px; border-radius: 14px;
        background: #ede9fe; color: #6d28d9;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; flex-shrink: 0;
    }
    .empty { text-align: center; padding: 60px 20px; color: #9ca3af; background: white; border: 1px solid #e5e7eb; border-radius: 18px; }
</style>
@endpush

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Private Chats</h1>
        <p class="text-sm text-gray-500 mt-1">Conversations with users from identified harassment reports and approved mentorship sessions.</p>
    </div>

    @if(session('success'))
    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if($conversations->isEmpty())
    <div class="empty">
        <i class="fas fa-comments text-5xl text-gray-300"></i>
        <p class="mt-4 text-lg font-semibold text-gray-500">No private chats yet</p>
        <p class="mt-1 text-sm">Open a chat from an identified harassment report to start a private conversation.</p>
    </div>
    @else
    @foreach($conversations as $conversation)
        @php
            $other = $conversation->participants->first(fn($participant) => $participant->id !== $mentor->id);
            $lastMessage = $conversation->messages->first();
        @endphp
        <a href="{{ route('mentor.chat.show', $conversation) }}" class="chat-row">
            <div class="avatar">
                {{ strtoupper(substr($other?->name ?? 'User', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-3">
                    <p class="truncate text-sm font-semibold text-gray-900">{{ $other?->name ?? 'User' }}</p>
                    @if($lastMessage)
                    <span class="whitespace-nowrap text-xs text-gray-400">{{ $lastMessage->created_at->diffForHumans() }}</span>
                    @endif
                </div>
                <p class="mt-1 truncate text-sm text-gray-500">
                    {{ $lastMessage?->message ?? 'No messages yet. Start the conversation from here.' }}
                </p>
            </div>
            <i class="fas fa-chevron-right text-gray-300"></i>
        </a>
    @endforeach

    <div class="mt-5">{{ $conversations->links() }}</div>
    @endif
</div>
@endsection
