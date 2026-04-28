@extends('mentor.layouts.dashboard')

@section('title') guidance @endsection

@section('content')
<div class="px-4 py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mt-10 mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Guidance Content</h1>
        <p class="mt-2 text-sm text-gray-600">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore, a dolores ea reprehenderit dolorum harum quia esse maiores, accusamus magnam quidem quibusdam vero suscipit recusandae libero..</p>
    </div>

    <!-- content Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

        <!-- hygiene_articles -->
        <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-blue-50">
                    <i class="w-6 h-6 text-blue-600 fa-solid fa-head-side-mask"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">hygiene_articles</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore,.</p>
            <a href="{{ route('mentor.hygiene')}}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                Navigate
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <!-- general_guides -->
        <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-green-50">
                    <i class="w-6 h-6 text-green-600 fa-brands fa-hubspot"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">general_guides</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore,.</p>
            <a href="{{ route('mentor.general')}}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                Navigate
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <!-- emergency_contacts -->
        <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-yellow-50">
                    <i class="w-6 h-6 text-yellow-600 fa-solid fa-tower-broadcast"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">emergency_contacts</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore.</p>
            <a href="{{ route('mentor.emergency')}}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                navigate
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>




    </div>


</div>
@endsection
