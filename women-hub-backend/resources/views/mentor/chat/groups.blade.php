@extends('mentor.layouts.dashboard')
@section('title')
    chat groups
@endsection

@section('content')

    {{-- <div class="flex flex-col items-center justify-center h-full">
        <h1>will be added soon</h1>
    </div> --}}


<div class="max-w-full mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Available Chat Groups</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            @for ($i = 0; $i < 3; $i++)

                <div class="bg-white shadow rounded-2xl p-4 hover:shadow-lg transition">

                    <h2 class="text-lg font-semibold">
                        Health & Wellness
                    </h2>

                    <p class="text-gray-600 text-sm mt-1">
                        A supportive space to discuss health, wellness, and self-care tips.
                    </p>

                    <div class="mt-3 flex justify-between items-center">

                        <span class="text-xs text-gray-500">
                            Members: 12
                        </span>

                        <a
                           class="text-blue-600 text-sm cursor-pointer font-medium hover:underline">
                           <i class="fa-solid fa-users"></i> Join Chat
                        </a>

                        {{-- <a href="{{ route('chat.groups.show', $group->id) }}"
                           class="text-blue-600 text-sm font-medium hover:underline">
                            Join Chat →
                        </a> --}}

                    </div>

                </div>

            @endfor
        </div>


</div>
@endsection
