@extends('mentor.layouts.dashboard')

@section('title') hygiene @endsection

@section('content')
    <div class="max-w-5xl px-4 py-0 mx-auto sm:px-6 lg:px-8">
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
                <span class="text-gray-900">Hygiene</span>
            </div>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-900">Hygiene Content</h1>
            <p class="mt-2 text-sm text-gray-600">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis excepturi fugiat, alias repudiandae aliquam praesentium ullam molestiae eum laudantium natus porro animi unde corrupti quisquam? Incidunt eaque non ex magnam!</p>
        </div>


        {{-- details --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">

            @foreach ($hygiene as $item)
                <div class="overflow-hidden transition-all duration-300 bg-white shadow-md group rounded-2xl hover:shadow-xl">
                    <!-- Image Section -->
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        @if ($item->image_url)
                            <img src="{{$item->image_url}}"
                            class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110"
                            alt="{{$item->title}}">
                        @else
                            <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-100 to-purple-200">
                                    <img src="{{ asset('images/Ellipse 3.png')}}"
                                        class="object-cover w-40 transition-transform duration-500 group-hover:scale-110"
                                        alt="{{$item->title}}">
                                </div>
                        @endif

                        <!-- Category Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="px-3 py-2 text-xs font-semibold text-white bg-purple-600 rounded-full shadow-lg">
                                {{$item->category ?? 'Uncategorized'}}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            @if ($item->is_published == 1)
                                <span class="px-2 py-2 text-xs font-semibold text-white bg-green-500 rounded-full shadow-lg">
                                    Published
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-white bg-yellow-500 rounded-full shadow-lg">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-5">
                        <!-- Title -->
                        <h3 class="mb-2 text-lg font-bold text-gray-800 transition-colors line-clamp-2 hover:text-purple-600">
                            <p  class="cursor-pointer ">{{$item->title}}</p>
                        </h3>

                        <!-- content -->
                        <p class="mb-4 text-sm text-gray-600 line-clamp-0">
                            {{$item->content}}
                        </p>

                        <!-- Footer with date -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{$hygieneCreatedAt}}</span>
                            </div>


                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
@endsection
