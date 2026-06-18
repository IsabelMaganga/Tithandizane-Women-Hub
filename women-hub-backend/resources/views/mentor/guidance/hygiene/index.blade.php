@extends('mentor.layouts.dashboard')

@section('title') Menstrual Hygiene @endsection

@section('content')
<div class="max-w-6xl px-4 py-0 mx-auto sm:px-6 lg:px-8">
    <div class="mt-10 mb-4">
        <a href="{{ route('mentor.Guidance') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-purple-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Guidance
        </a>
    </div>

    <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Menstrual Hygiene Guidance</h1>
            <p class="mt-2 text-sm text-gray-600">Your published and unpublished menstrual hygiene posts.</p>
        </div>
        <a href="{{ route('mentor.hygiene.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9]">
            <i class="mr-2 fas fa-plus"></i> Publish New
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-green-800 bg-green-100 border border-green-200 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($hygiene->isEmpty())
        <div class="p-10 text-center bg-white border border-dashed border-gray-200 rounded-2xl">
            <p class="text-gray-500">No content yet. Create your first menstrual hygiene article.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($hygiene as $item)
                <div class="flex flex-col overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
                    @if($item->photo_url)
                        <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" class="w-full h-36 object-cover">
                    @endif
                    <div class="flex flex-col flex-1 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 text-xs font-semibold uppercase rounded-full {{ $item->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $item->status }}
                            </span>
                        </div>
                        <h2 class="mb-2 text-lg font-bold text-gray-900">{{ $item->title }}</h2>
                        <p class="flex-1 mb-4 text-sm text-gray-600 line-clamp-4">{{ $item->body }}</p>
                        <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('mentor.hygiene.edit', $item->id) }}" class="px-3 py-1.5 text-xs font-medium text-[#7c3aed] bg-violet-50 rounded-lg hover:bg-violet-100">Edit</a>
                            @if($item->status === 'published')
                                <form action="{{ route('mentor.content.unpublish', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100">Unpublish</button>
                                </form>
                            @else
                                <form action="{{ route('mentor.content.publish', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100">Publish</button>
                                </form>
                            @endif
                            <form action="{{ route('mentor.content.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this content permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
