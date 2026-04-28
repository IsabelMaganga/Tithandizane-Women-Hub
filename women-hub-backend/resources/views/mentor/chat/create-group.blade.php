@extends('mentor.layouts.dashboard')
@section('title')
    create-group
@endsection

@section('content')

   <div class="grid w-full h-full max-w-full grid-cols-2 gap-10 p-6 mx-auto ">

        <div class="p-1 bg-white shadow left-section rounded-2xl">

            <h1 class="px-5 mt-5 mb-3 text-2xl font-bold">Create Chat Group</h1>

            {{-- <form action="{{ route('chat.groups.store') }}" method="POST" class="p-6 bg-white shadow rounded-2xl"> --}}
            <form  method="POST" class="p-6 bg-white ">
                @csrf

                <!-- Group Name -->
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Group Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full p-2 text-sm border-2 rounded-lg outline-1 focus:outline-blue-500/70"
                        placeholder="Enter group name">

                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{--  title   --}}
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Title</label>
                    <input name="title"
                            class="w-full p-2 text-sm border-2 rounded-lg outline-1 outline-blue-400 focus:outline-blue-500/70"
                            placeholder="Type a short title ( minimum 15 characters)">{{ old('title') }}</input>

                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Description</label>
                    <textarea name="description"
                            class="w-full p-2 text-sm border-2 rounded-lg outline-1 outline-blue-400 focus:outline-blue-500/70"
                            rows="3"
                            placeholder="Optional description">{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{--  Image Upload Controls  --}}
                <div class="flex-1 mb-2">
                    <div class="flex items-center space-x-3">
                        <label for="profile-image"
                               class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span>Upload new photo</span>
                            <input type="file" id="profile-image" class="hidden" accept="image/*">
                        </label>
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Remove
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">JPG, GIF or PNG. Max size 2MB.</p>
                </div>

                {{--  <!-- Privacy -->  --}}
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Privacy</label>
                    <select name="is_private"
                            class="w-full p-2 text-sm rounded-lg outline-1 focus:outline-blue-500/70">
                        <option value="0">Public</option>
                        <option value="1">Private</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                        Create Group
                    </button>
                </div>

            </form>

        </div>

        <div class="flex flex-col justify-start right-section">
            <h1 class="mt-5 text-2xl ">Rules to follow</h1>
            <ul class="flex flex-col gap-3 px-10 text-sm text-gray-700 list-disc ">
                <li>Clear name</li>
                <li>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas consequuntur nulla eum dolores illum magni odit aut dolorum ea reprehenderit vitae repellendus, sint sequi eaque esse possimus nobis nisi numquam!</li>
                 <li>clear description</li>
                <li>clear description</li>
                <li>clear description</li>
                 <li>clear description</li>
                <li>clear description</li>
                 <li>clear description</li>
                <li>clear description</li>
                <li>clear description</li>
                <li>clear description</li>
                 <li>clear description</li>
                <li>clear description</li>

            </ul>
        </div>

    </div>

@endsection
