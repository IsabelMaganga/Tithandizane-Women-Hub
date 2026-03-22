@extends('mentor.layouts.dashboard')

@section('title') emergency @endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 ">
     <!-- Header with Back Navigation -->
    <div class="mb-4">

        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('mentor.Guidance') }}" class="hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>guidance</span>
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900">emergency</span>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        {{-- <h1 class="text-3xl font-semibold text-gray-900">emergency Content</h1> --}}
    </div>




    {{-- contact form --}}
    <div class=" w-full overflow-x-auto gap-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class=" bg-[#111827] divide-y px-3 py-3 capitalize text-left">
                <tr class=" divide-y  ">
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">#</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">name</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">type</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">phone</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">region</th>
                    <th scope="col" class=" px-5 py-4 text-[#fff] tracking-wider text-left text-sm uppercase ">Status</th>
                </tr>
            </thead>
            <tbody class="min-w-full bg-white divide-y divide-gray-200">
                @forelse($contact as $contact)
                <tr class=" hover:bg-slate-50/40 divide-y cursor-pointer duration-100 ">
                    <td class=" px-5 w-1 bg-[#111827] text-amber-50 py-4">{{ $contact->id}}</td>
                    <td class=" px-5 py-4">{{ $contact->name }}</td>
                    <td class=" px-5 py-4 whitespace-nowrap capitalize  ">{{$contact->type}}</td>
                    <td class=" px-5 py-4 flex gap-1 items-center"><i class="fa-solid fa-phone text-blue-500"></i><a href="tel:{{$contact->phone}}" class=" text-blue-500">{{$contact->phone}}</a></td>
                    <td class=" px-5 py-4 capitalize">{{$contact->region}}</td>
                    <td class=" flex justify-center items-center">
                        @if ($contact->is_active)
                            <p class=" w-7 mt-4 rounded-full h-7 flex justify-center item-center ">
                                <i class="fa-regular  fa-circle-check text-xl text-green-500 "></i>
                            </p>
                        @else
                            <p class=" w-5 mt-4 rounded-full h-5 ">
                                <i class="fa-regular fa-circle-xmark text-xl text-red-500"></i>
                            </p>
                        @endif
                    </td>
                </tr>
                @empty
                <div>
                    <div colspan="6" class="text-center py-5" style="color:#B8A0B0;">
                        <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        No contacts found.
                    </div>
                </div>
                @endforelse
            </p>
        </div>

    </div>
@endsection
