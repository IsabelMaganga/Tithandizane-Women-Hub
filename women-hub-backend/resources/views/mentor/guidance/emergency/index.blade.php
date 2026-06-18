@extends('mentor.layouts.dashboard')

@section('title') emergency @endsection

@section('content')
<div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8 ">
     <!-- Header with Back Navigation -->
    <div class="mt-10 mb-4">

        <div class="flex items-center gap-2 mb-2 text-sm text-gray-600">
            <a href="{{ route('mentor.Guidance') }}" class="flex items-center gap-1 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>guidance</span>
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900">emergency</span>
        </div>
    </div>


     <!-- Header Section -->
        <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-600 p-8 md:p-10">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-10 -mb-10"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="text-2xl text-white fas fa-hand-sparkles"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white md:text-4xl">emergency</h1>
                    </div>
                </div>
                <p class="max-w-2xl text-purple-100 text-sm md:text-base leading-relaxed">
                    Explore our comprehensive collection of hygiene resources designed to promote health, wellness, and cleanliness in your community. Stay informed with expert guidelines and practical tips.
                </p>


            </div>
        </div>




    {{-- contact form --}}
    <div class="w-full gap-4 overflow-x-auto ">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class=" bg-[#111827] divide-y px-3 py-3 capitalize text-left">
                <tr class="divide-y ">
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
                <tr class="duration-100 divide-y cursor-pointer  hover:bg-slate-50/40">
                    <td class=" px-5 w-1 bg-[#111827] text-amber-50 py-4">{{ $contact->id}}</td>
                    <td class="px-5 py-4 ">{{ $contact->name }}</td>
                    <td class="px-5 py-4 capitalize  whitespace-nowrap">{{$contact->type}}</td>
                    <td class="flex items-center gap-1 px-5 py-4 "><i class="text-blue-500 fa-solid fa-phone"></i><a href="tel:{{$contact->phone}}" class="text-blue-500 ">{{$contact->phone}}</a></td>
                    <td class="px-5 py-4 capitalize ">{{$contact->region}}</td>
                    <td class="flex items-center justify-center ">
                        @if ($contact->is_active)
                            <p class="flex justify-center mt-4 rounded-full  w-7 h-7 item-center">
                                <i class="text-xl text-green-500 fa-regular fa-circle-check "></i>
                            </p>
                        @else
                            <p class="w-5 h-5 mt-4 rounded-full ">
                                <i class="text-xl text-red-500 fa-regular fa-circle-xmark"></i>
                            </p>
                        @endif
                    </td>
                </tr>
                @empty
                <div>
                    <div colspan="6" class="py-5 text-center" style="color:#B8A0B0;">
                        <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        No contacts found.
                    </div>
                </div>
                @endforelse
            </p>
        </div>

    </div>
@endsection
