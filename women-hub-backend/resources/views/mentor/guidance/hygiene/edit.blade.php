@extends('mentor.layouts.dashboard')
@section('title') Edit Menstrual Hygiene Content @endsection

@section('content')
<div class="max-w-4xl px-4 py-0 mx-auto sm:px-6 lg:px-8">
    <div class="mt-10 mb-4">
        <a href="{{ route('mentor.hygiene') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-purple-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
        <h1 class="mt-4 text-3xl font-semibold text-gray-900">Edit Menstrual Hygiene Content</h1>
    </div>

    @if($errors->any())
        <div class="p-4 mb-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="p-6 bg-white rounded-lg shadow">
        <form action="{{ route('mentor.hygiene.update', $content->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $content->title) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed]">
                </div>
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Content *</label>
                    <textarea name="body" id="body" rows="10" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed]">{{ old('body', $content->body) }}</textarea>
                </div>
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Cover Photo <span class="text-gray-400 font-normal">(optional)</span></label>
                    @if($content->photo_url)
                        <div class="mt-2 mb-3">
                            <img src="{{ $content->photo_url }}" alt="Current cover" class="h-32 w-auto rounded-lg object-cover border border-gray-200">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300 text-[#7c3aed] focus:ring-[#7c3aed]">
                            Remove current photo
                        </label>
                    @endif
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-[#6d28d9] hover:file:bg-violet-100">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1"
                           {{ old('is_published', $content->status === 'published') ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="is_published" class="ml-2 text-sm text-gray-900">Published</label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('mentor.hygiene') }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 text-sm text-white bg-[#7c3aed] rounded-md hover:bg-[#6d28d9]">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
