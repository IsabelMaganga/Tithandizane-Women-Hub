@extends('mentor.layouts.dashboard')

@section('title') home @endsection

@section('content')
    {{-- header --}}
    <div class="flex px-3 mt-3 justify-between items-center">
        <h1 class="text-3xl mb-6 capitalize">Welcome back</h1>
        <h1 id="time" class="text-3xl mb-6 capitalize">time</h1>
    </div>

    {{-- stst card --}}
    <div class="grid relative px-3 gap-5 grid-cols-1 backdrop-blur-2xl md:grid-cols-3">

        {{-- mentor short detail --}}
        <div class="group relative bg-gradient-to-t from-purple-800 to-purple-800 wrap-break-word break-all transition delay-200 hover:shadow-sm rounded-xl p-7 shadow cursor-pointer">
            <div class="flex justify-between items-center">
                <p class="text-sm text-purple-400">Mentor</p>
                <p class="text-sm bg-green-500 w-5 h-5 rounded-full"></p>
            </div>

            <div class="flex justify-start gap-2 mt-2 items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff&size=128"
                     class="w-10 h-10 rounded-full">
                <h2 class="text-xl font-semibold text-gray-100">{{ $mentorName }}</h2>
            </div>

            <p class="text-sm mt-1 text-purple-200">{{ $mentorEmail }}</p>
            <p class="text-sm mt-1 text-purple-300">
                Here's what's happening with your platform today
            </p>
        </div>

        {{-- total chats --}}
        <div class="bg-white rounded-lg cursor-pointer shadow p-6 transition delay-200 hover:shadow-sm">
            <div class="flex flex-col items-start">
                <div class="flex justify-between w-full items-center">
                    <div class="p-3 bg-blue-100 w-20 flex items-center justify-between rounded-full">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                        <i class="fas fa-plus text-blue-600"></i>
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-2xl p-2 px-3 bg-[#f3f4f6] border-[#2563eb]/20 border-2 rounded-full font-normal">
                        20
                    </p>
                </div>

                <div class="mt-2 flex w-full items-center justify-between">
                    <p class="text-3xl text-gray-700">Total chats</p>
                </div>

                <p class="text-sm text-gray-400 mt-1">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi il
                </p>
            </div>
        </div>

        {{-- active chats --}}
        <div class="bg-white rounded-lg cursor-pointer shadow p-6 transition delay-200 hover:shadow-sm">
            <div class="flex flex-col items-start">
                <div class="flex justify-between w-full items-center">

                    <div class="mt-2 flex w-full items-center justify-between">
                        <p class="text-3xl text-gray-700">Active chats</p>
                    </div>
                    <p class="text-xl h-10 w-10 flex justify-center items-center bg-[#f3f4f6] border-[#2563eb]/20 border-2 rounded-full font-normal"> {{ $activeChats}} </p>

                </div>

                <p class="text-sm text-gray-400 mt-1">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequiil
                </p>
            </div>
        </div>

    </div>

    <div id="pending-chats-section" class="div grid grid-cols-1 mt-2 w-full  relative mx-auto md:grid-cols-2">
        @include('mentor.partials.pending-chats', ["pendingChats" => $pendingChats])
    </div>

    {{-- Availability --}}
    <div class="bg-white mx-3 p-4 rounded shadow">
        <h2 class="text-lg font-bold mb-4">Availability</h2>

        <form action="#" method="POST">
            @csrf
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_available" onchange="this.form.submit">
                <p>Offline</p>
            </label>
        </form>
    </div>

    {{-- Extra Cards --}}
    <div class="bg-white col-span-3 mx-3 mt-5 transition delay-200 hover:shadow-sm rounded-xl p-7 shadow cursor-pointer">
        <p class="text-sm mt-1 text-gray-400">
            Here's what's happening with your platform today
        </p>
    </div>

    {{-- Footer Links --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mx-3 mt-5">
        <p class="text-sm rounded-xl p-5 shadow cursor-pointer bg-white text-gray-400">Settings</p>
        <p class="text-sm rounded-xl p-5 shadow cursor-pointer bg-white text-gray-400">Guidance</p>
        <p class="text-sm rounded-xl p-5 shadow cursor-pointer bg-white text-gray-400">Guidance</p>
        <p class="text-sm rounded-xl p-5 shadow cursor-pointer bg-red-500 text-gray-100">Logout</p>
    </div>


@push('scripts')
<script>

    // Listen for new chat requests instantly
    window.Echo.private('mentor.' . {{ auth()->id() }})
        .listen('NewChatRequest', (e) => {
            // Add new request instantly
            addNewChatRequest(e);

            console.log('test');
            // Play sound
            playNotificationSound();

            // Show browser notification
            // showNotification(`${e.girl_name} wants to chat`);
        });

    function addNewChatRequest(data) {
        const container = document.getElementById('pending-chats-section');
        const newRequestHtml = `
            <div class="flex justify-between items-center border-b py-3" data-chat-id="${data.id}">
                <div>
                    // <p class="font-semibold">${data.}</p>
                    <p class="text-sm text-gray-500">${data.created_at}</p>
                </div>
                <div class="space-x-2">
                    <button class="bg-green-500 text-white px-4 py-1 rounded accept-btn"
                            data-url="/mentor/chat/accept/${data.id}">Accept</button>
                    <button class="bg-red-500 text-white px-4 py-1 rounded reject-btn"
                            data-url="/mentor/chat/reject/${data.id}">Reject</button>
                </div>
            </div>
        `;

        // Add to top of list
        container.insertAdjacentHTML('afterbegin', newRequestHtml);

        // Remove "No pending requests" message if exists
        const emptyMessage = container.querySelector('p.text-gray-500');
        if (emptyMessage && container.children.length === 1) {
            emptyMessage.remove();
        }

        // Highlight the new item
        const newItem = container.querySelector(`[data-chat-id="${data.id}"]`);
        if (newItem) {
            newItem.style.backgroundColor = '#fef3c7';
            setTimeout(() => {
                newItem.style.backgroundColor = '';
            }, 2000);
        }
    }

    // Function to fetch and update only the pending chats section
    // function updatePendingChats() {
    //     fetch('{{ route("mentor.dashboard") }}', {
    //         method: 'GET',
    //         headers: {
    //             'X-Requested-With': 'XMLHttpRequest',
    //             'Accept': 'text/html',
    //         }
    //     })
    //     .then(response => response.text())
    //     .then(html => {
    //         // Update only the specific section
    //         const section = document.getElementById('pending-chats-section');
    //         if (section) {
    //             section.innerHTML = html;
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error updating chats:', error);
    //     });
    // }

    // Auto update every 3 seconds
    // let intervalId = setInterval(updatePendingChats, 3000);

    // Optional: Stop interval when page is hidden to save resources
    // document.addEventListener('visibilitychange', function() {
    //     if (document.hidden) {
    //         clearInterval(intervalId);
    //     } else {
    //         intervalId = setInterval(updatePendingChats, 3000);
    //         updatePendingChats(); // Refresh immediately when tab becomes visible
    //     }
    // });

    // Clean up interval when leaving page
    // window.addEventListener('beforeunload', function() {
    //     clearInterval(intervalId);
    // });
</script>
@endpush

@endsection
