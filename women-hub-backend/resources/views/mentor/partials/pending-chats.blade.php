{{-- Pending Requests --}}
@forelse($pendingChats as $chat)
    <div class="flex bg-white p-4 mt-2 mb-2 mx-3 justify-between items-center border-b py-3">

        <div class=" text-md">
            <p class=" text-gray-800">Name:  <span>{{ $chat->mentee->name }}</span> </p>
            <p class=" text-gray-800">Topic: <span>{{ $chat->topic }}</span> </p>
            <p class="text-sm text-gray-500">{{ $chat->created_at->diffForHumans() }} </p>
        </div>

        <div class="space-x-2">

            <!-- Accept -->
            {{-- <form method="POST" action="{{ route('mentor.chat.accept', $chat->id) }}" class="inline"> --}}
            <form method="POST"  class="inline">
                @csrf
                <button class="bg-green-500 text-white px-4 py-1 rounded">
                    Accept
                </button>
            </form>

            <!-- Reject -->
            {{-- <form method="POST" action="{{ route('mentor.chat.reject', $chat->id) }}" class="inline"> --}}
            <form method="POST"  class="inline">
                @csrf
                <button class="bg-red-500 text-white px-4 py-1 rounded">
                    Reject
                </button>
            </form>

        </div>

    </div>
@empty
    <div class=" mx-3 p-3 bg-white sticky top-0 shadow rounded mt-3 mb-3">
        <p >No pending requests</p>
    </div>
@endforelse



