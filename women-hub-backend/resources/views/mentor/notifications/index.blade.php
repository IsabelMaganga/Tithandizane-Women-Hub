@extends('mentor.layouts.dashboard')

@section('title') notifications @endsection

@section('content')
<div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8 ">

     <!-- Header with Back Navigation -->
    <div class="mt-10 mb-4">

        <div class="flex items-center gap-2 mb-2 text-sm text-gray-600">
            <a href="{{ route('mentor.settings') }}" class="flex items-center gap-1 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>settings</span>
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900">notifications</span>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-2">
        <h1 class="text-3xl font-semibold text-gray-900">notifications</h1>
    </div>

    <div class="mb-2 flex justify-between items-center">
        <h1 class="text-3xl font-semibold text-gray-900"></h1>

        <div class="mb-2 flex justify-between gap-4 items-center">

            <a id="refresh"  class="bg-gray-500 hover:bg-black rounded-xl text-white hover:text-gray-100 cursor-pointer transition-all delay-75 text-sm select-none p-2 px-3">
                <i class="fa-solid fa-arrow-rotate-right"></i>
            </a>
            <select class="bg-gray-500 rounded-xl text-white hover:text-gray-100 cursor-pointer transition-all delay-75 text-sm select-none p-2 px-4">
                <option value="all">All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>

            <form action="{{ route('mentor.notification.read-all') }}" method="POST" class="bg-green-500 rounded-xl text-white hover:text-gray-100 cursor-pointer transition-all delay-75 text-sm select-none p-2 px-3">
                @csrf
                <button type="submit">Mark all as read</button>
            </form>
        </div>

    </div>

    {{-- notification form --}}
    <div id="notification-list" class="w-full gap-4 overflow-x-auto ">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class=" bg-[#111827] divide-y px-3 py-3 capitalize text-left">
                <tr class="divide-y select-none">
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">ID</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">name</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">message</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">Read_at</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">Action</th>
                </tr>
            </thead>
            <tbody class="min-w-full bg-white divide-y divide-gray-200">

                    @if ($notifications->isEmpty())
                        <tr class="divide-y">
                            <td colspan="5" class="px-5 py-4 whitespace-nowrap text-sm text-gray-900 text-center">No notifications found.</td>
                        </tr>
                    @else

                        @foreach ($notifications as $notification)
                        <tr class="divide-y">
                            <td class="px-5 py-4 truncate text-sm text-gray-900">{{ $notification->id ? 1 : 'N/A' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">{{ auth()->user()->name   }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">{{ $notification->data['message'] }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">{{ $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : 'null' }}</td>
                            <td class=" whitespace-nowrap h-full w-full bg-green-500">
                                <form action="{{ route('mentor.notification.read', $notification->id) }}" method="POST" class="  text-white font-bold hover:text-gray-100 cursor-pointer transition-all delay-75 text-[10px]  px-3">
                                    @csrf
                                    <button type="submit">Mark as read</button>
                                </form>
                            </td>

                        </tr>
                        @endforeach

                    @endif

            </tbody>
        </table>

    </div>

    <div id="refresh-spinner" class="hidden w-full flex flex-col gap-3 text-3xl justify-center mt-4 items-center">
        <div class="loading border-t-2 border-r-2 animate-spin ease-in-out delay-1000  border-blue-500 w-20 h-20 rounded-full"></div>
        <h1>Refreshing...</h1>
    </div>

</div>


@endsection


@push('scripts')
    <script>
        console.log('notifications page loaded');

        const refresh = document.getElementById('refresh');
        const refreshSpinner = document.getElementById('refresh-spinner');
        const notificationList = document.getElementById('notification-list');

        if (refresh) {

            refresh.addEventListener('click', () => {
                refreshSpinner.classList.remove('hidden');
                notificationList.classList.add('hidden');
                console.log('refresh button pressed');

                setTimeout(() => {
                    refreshSpinner.classList.add('hidden');
                    notificationList.classList.remove('hidden');
                    location.reload();
                }, 4000);
            });
        }

    </script>
@endpush
