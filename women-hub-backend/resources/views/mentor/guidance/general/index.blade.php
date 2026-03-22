@extends('mentor.layouts.dashboard')

@section('title') general @endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
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
            <span class="text-gray-900">general</span>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8 mt-2">
        <h1 class="text-3xl font-semibold text-gray-900">general Content</h1>
        <p class="mt-2 text-sm text-gray-600">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Harum, vitae quasi cupiditate esse itaque architecto. Qui corporis tempora saepe, ut hic placeat possimus, ab enim, repellendus officiis optio tempore debitis.</p>
    </div>




    {{-- details --}}
    <div class=" w-full  grid grid-cols-1 md:grid-cols-3  gap-4">

        @foreach ($general as $item)
            <div class="mb-8 p-3 grid hover:scale-105 transition delay-200 overflow-hidden bg-[#fff] gap-3 shadow-xl rounded-xl  grid-cols-1 ">

                <div class="col-span-1 flex flex-col w-full items-start justify-center px-2">
                    <div class="div flex flex-col flex-1 w-full">
                        <h1 class=" text-md flex font-bold">{{$item->title}}</h1>
                        <p class=" text-[12px] text-gray-600 mt-2 select-none">{{$item->content}}</p>
                    </div>

                    <div class=" flex justify-start grid-cols-2 mx-auto gap-3 bg-gray-100/10 py-2 px-2  rounded-full mt-3 w-full items-center  text-gray-500 capitalize" >

                        <p class=" bg-purple-700 hover:bg-purple-800 active:bg-purple-900 select-none transition delay-75 p-3 text-center shadow cursor-pointer text-[10px] rounded-3xl text-amber-50">{{$item->icon}}</p>
                        @if ($item->is_published == 1)
                            <p class=" bg-purple-700 w-full hover:bg-purple-800 active:bg-purple-900 select-none transition delay-75 p-3 text-center shadow cursor-pointer text-[12px] rounded-3xl text-amber-50">Published</p>
                        @else
                            <p class=" bg-purple-700 w-full hover:bg-purple-800 active:bg-purple-900 select-none transition delay-75 p-3 text-center shadow cursor-pointer text-[12px] rounded-3xl text-amber-50">Not Published</p>
                        @endif


                    </div>
                </div>

            </div>
        @endforeach

     </div>


</div>
@endsection
