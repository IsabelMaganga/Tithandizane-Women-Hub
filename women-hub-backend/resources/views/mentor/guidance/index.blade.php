@extends('mentor.layouts.dashboard')

@section('title') guidance @endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Guidance Content</h1>
        <p class="mt-2 text-sm text-gray-600">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore, a dolores ea reprehenderit dolorum harum quia esse maiores, accusamus magnam quidem quibusdam vero suscipit recusandae libero..</p>
    </div>

    <!-- content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- hygiene_articles -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fa-solid fa-head-side-mask w-6 h-6 text-blue-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">hygiene_articles</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore,.</p>
            <a href="{{ route('mentor.hygiene')}}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Navigate
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <!-- general_guides -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-green-50 rounded-lg">
                    <i class="fa-brands fa-hubspot w-6 h-6 text-green-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">general_guides</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore,.</p>
            <a href="{{ route('mentor.general')}}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Navigate
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <!-- emergency_contacts -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-yellow-50 rounded-lg">
                    <i class="fa-solid fa-tower-broadcast w-6 h-6 text-yellow-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">emergency_contacts</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt sed commodi, architecto tempore.</p>
            <a href="{{ route('mentor.emergency')}}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                navigate
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>




    </div>


</div>
@endsection
