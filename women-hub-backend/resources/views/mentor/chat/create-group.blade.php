@extends('mentor.layouts.dashboard')
@section('title')
    chat-group-creation
@endsection

@section('content')

   <div class="max-w-2xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Create Chat Group</h1>

    {{-- <form action="{{ route('chat.groups.store') }}" method="POST" class="bg-white p-6 rounded-2xl shadow"> --}}
    <form  method="POST" class="bg-white p-6 rounded-2xl shadow">
        @csrf

        <!-- Group Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Group Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full rounded-lg p-2 outline-1 focus:outline-blue-500/70"
                   placeholder="Enter group name">

            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description"
                      class="w-full rounded-lg p-2 outline-1 focus:outline-blue-500/70"
                      rows="3"
                      placeholder="Optional description">{{ old('description') }}</textarea>

            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Privacy -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Privacy</label>
            <select name="is_private"
                    class="w-full rounded-lg p-2 outline-1 focus:outline-blue-500/70">
                <option value="0">Public</option>
                <option value="1">Private</option>
            </select>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Create Group
            </button>
        </div>

    </form>

</div>

@endsection
