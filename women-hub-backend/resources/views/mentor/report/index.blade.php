@extends('mentor.layouts.dashboard')
@section('title')
    Report
@endsection

@section('content')
      <div class="grid w-full h-full max-w-full grid-cols-2 gap-10 p-6 mx-auto ">
        <div class="w-full col-span-2 header">
            <h1 class="text-4xl font-semibold">Reports Issues here</h1>
        </div>

        <div class="p-1 bg-white shadow left-section rounded-2xl">



            {{-- <form action="{{ route('chat.groups.store') }}" method="POST" class="p-6 bg-white shadow rounded-2xl"> --}}
            <form  method="POST" action="{{ route('mentor.submit.report') }}" class="p-6 bg-white ">
                @csrf

                <!--  Name -->
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Username <span class="text-gray-400 ">( optional )</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full p-2 text-sm border-2 rounded-lg outline-1 focus:outline-blue-500/70"
                        placeholder="Your name">

                </div>

                 @error('username')
                    <p id="error" class="mt-1 mb-2 text-sm text-red-600 error">{{ $message }}</p>
                @enderror

              {{-- Issue Title --}}
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Issue Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full p-2 text-sm border-2 rounded-lg outline-1 outline-blue-400 focus:outline-blue-500/70"
                        placeholder="Type a short title (minimum 15 characters)" />
                </div>


                @error('title')
                    <p id="error" class="mt-1 mb-2 text-sm text-red-600 error">{{ $message }}</p>
                @enderror

                {{--  issue type  --}}
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Issue Type</label>
                    <select name="type"
                            class="w-full p-2 text-sm rounded-lg outline-1 focus:outline-blue-500/70">
                        <option value="null">Select...</option>
                        <option value="bug">Bug</option>
                        <option value="feedback">Feedback</option>
                        <option value="request">Request</option>
                        <option value="other">Other...</option>
                    </select>
                </div>


                @error('type')
                    <p id="error" class="mt-1 mb-2 text-sm text-red-600 error">{{ $message }}</p>
                @enderror

                {{-- Issue Date --}}
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Issue Date</label>
                    <input type="datetime-local" name="issue_date"
                        class="w-full p-2 text-sm border-2 rounded-lg outline-1 focus:outline-blue-500/70" />
                </div>

                @error('issue_date')
                    <p id="error" class="mt-1 mb-2 text-sm text-red-600 error">{{ $message }}</p>
                @enderror

                <!-- Description -->
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Report Description</label>
                    <textarea name="description"
                            class="w-full p-2 text-sm border-2 rounded-lg outline-1 outline-blue-400 focus:outline-blue-500/70"
                            rows="20"
                            placeholder="Write description of your issue (min 200 charaters)">{{ old('description') }}</textarea>


                </div>

                  @error('description')
                    <p id="error" class="mt-1 mb-2 text-sm text-red-600 error">{{ $message }}</p>
                @enderror

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                        Generate
                    </button>
                </div>

            </form>

        </div>

        <div class="flex flex-col justify-start right-section">
             <div class="img">
                <img src="{{  asset('/loginpng.png') }}" alt="">
            </div>

            <h1 class="mt-5 text-2xl ">Rules to generate a report</h1>
            <ul class="flex flex-col gap-3 px-10 text-sm text-gray-700 list-disc ">
                <li>Clear name</li>
                <li>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas consequuntur nulla eum dolores illum magni odit aut dolorum ea reprehenderit vitae repellendus, sint sequi eaque esse possimus nobis nisi numquam!</li>
                 <li>clear description</li>
                <li>clear description</li>
                <li>clear description</li>
                 <li>clear description</li>
            </ul>


        </div>

        <div class="flex items-center justify-between w-full col-span-2 text-sm header">
            <a href="{{  route('mentor.pending.reports') }}">see pending</a>

            <div class="flex items-center justify-between gap-3 header">
                 <p>contact us</p>
                <p>Help centre</p>
                <p>Tithandizane@org.com</p>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>

        window.addEventListener('DOMContentLoaded', ()=>{

            const msgBoxes = document.querySelectorAll('.error');

            msgBoxes.forEach(msgBox => {
                msgBox.style.display = 'flex';

                setTimeout(() => {
                    msgBox.style.display = 'none';
                }, 5000);
            });
        })
    </script>
@endpush
