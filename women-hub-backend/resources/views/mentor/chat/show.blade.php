@extends('mentor.layouts.dashboard')
@section('title') Chat with {{ $other?->name ?? 'User' }} @endsection

@push('styles')
<style>
    .chat-shell { height: 78vh; background: white; border: 1px solid #e5e7eb; border-radius: 18px; overflow: hidden; display: flex; flex-direction: column; }
    .chat-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 20px; background: #f8fafc; }
    .message-row { display: flex; margin-bottom: 12px; }
    .message-row.mentor { justify-content: flex-end; }
    .bubble { max-width: 70%; border-radius: 18px; padding: 12px 14px; line-height: 1.5; font-size: 14px; white-space: pre-wrap; }
    .mentor .bubble { background: #7c3aed; color: white; border-bottom-right-radius: 4px; }
    .user .bubble { background: white; color: #111827; border: 1px solid #e5e7eb; border-bottom-left-radius: 4px; }
    .meta { margin-top: 4px; font-size: 11px; color: #6b7280; }
    .mentor .meta { color: #ddd6fe; text-align: right; }
    .chat-form { border-top: 1px solid #e5e7eb; padding: 14px; display: flex; gap: 10px; background: white; }
    .chat-form textarea { flex: 1; min-height: 44px; max-height: 140px; resize: vertical; border: 1px solid #e5e7eb; border-radius: 14px; padding: 11px 14px; font: inherit; outline: none; }
    .chat-form textarea:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
    .send-btn { border: 0; border-radius: 14px; background: #7c3aed; color: white; padding: 0 20px; font-weight: 700; }
    .send-btn:disabled { opacity: .6; cursor: not-allowed; }
</style>
@endpush

@section('content')
@php $isCompletedSession = $session?->status === 'completed'; @endphp
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('mentor.chat') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-purple-700 hover:text-purple-900">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        @if($session && $session->status === 'accepted')
            <form action="{{ route('mentor.chat.end-session', $conversation) }}" method="POST" onsubmit="return confirm('End this mentorship session now?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-100">
                    <i class="fas fa-stop-circle"></i> End Session
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="chat-shell">
        <div class="chat-header">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-100 font-bold text-purple-700">
                    {{ strtoupper(substr($other?->name ?? 'User', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $other?->name ?? 'User' }}</h1>
                    <p class="text-xs text-gray-500">{{ $other?->email ?? 'Private report conversation' }}</p>
                </div>
            </div>
            <!-- <a href="{{ route('mentor.harassment.index') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                Cases
            </a> -->
        </div>

        <div class="chat-messages" id="chatMessages">
            @forelse($conversation->messages as $message)
                @php $isMentor = $message->sender_id === $mentor->id; @endphp
                <div class="message-row {{ $isMentor ? 'mentor' : 'user' }}">
                    <div>
                        <div class="bubble">{{ $message->message }}</div>
                        <div class="meta">{{ $isMentor ? 'You' : ($message->sender?->name ?? 'User') }} · {{ $message->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            @empty
                <div class="flex h-full items-center justify-center text-center text-gray-400">
                    <div>
                        <i class="fas fa-comment-dots text-4xl"></i>
                        <p class="mt-3 font-medium">No messages yet</p>
                        <p class="text-sm">Start the private conversation with this user.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($isCompletedSession)
            <div class="chat-form justify-center">
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                    This conversation was completed. Messaging is closed.
                </div>
            </div>
        @else
            <form class="chat-form" method="POST" action="{{ route('mentor.chat.send', $conversation) }}">
                @csrf
                <textarea name="message" id="messageInput" placeholder="Type a private message..." required></textarea>
                <button type="submit" class="send-btn" id="sendBtn">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    function appendMessage(message) {
        if (!chatMessages) return;

        const row = document.createElement('div');
        row.className = 'message-row mentor';
        row.innerHTML = `
            <div>
                <div class="bubble">${message.message}</div>
                <div class="meta">You · Just now</div>
            </div>
        `;
        chatMessages.appendChild(row);
        scrollToBottom();
    }

    scrollToBottom();

    messageInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendBtn?.click();
        }
    });

    if (window.Echo) {
        const channelName = 'chat.{{ $conversation->id }}';
        window.Echo.private(channelName)
            .listen('App\\Events\\MessageSent', (event) => {
                if (event.message?.message) {
                    appendMessage(event.message);
                }
            });
    }
</script>
@endpush
