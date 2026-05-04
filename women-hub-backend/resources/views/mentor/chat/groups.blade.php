@extends('mentor.layouts.dashboard')
@section('title')
    chat groups
@endsection

@push('styles')
    <style>
            #show-group{
                display: flex;
            }
            {{--  #popup-window{
                display: none;
            }  --}}
    </style>
@endpush

@section('content')

    {{-- <div class="flex flex-col items-center justify-center h-full">
        <h1>will be added soon</h1>
    </div> --}}


<div class="relative w-full h-full max-w-full p-6 mx-auto">

    <h1 class="mb-6 text-2xl font-bold">Available Chat Groups</h1>


        <div class="grid grid-cols-1 gap-5 md:grid-cols-4 group-card">

            @for ($i = 0; $i < 4; $i++)


            <div class="grid group-card">

            {{--  <div  id="show-group"  class="rounded-full cursor-pointer hover:scale-105 select-none transition-all ease-in-out images contain-content w-[20vh] border-4 border-[#f2e] h-[20vh] ">
                <img src="{{ asset('/images/background.png') }}" class="object-cover w-full h-full " alt="group image">
            </div>  --}}

                <div id="popup-window" class="top-0 right-0 z-20 flex items-center justify-center w-full pop-image">

                    <div class="p-4 transition w-[100%] bg-white shadow rounded-2xl hover:shadow-lg">
                        {{--  <div class="flex items-center justify-end w-full p-0 cursor-pointer close-mark">
                            <i id="xmark-btn" class="text-2xl fa-solid fa-xmark"></i>
                        </div>  --}}
                        <div class=" w-50 header bg-amber-800 contain-content h-[30vh]">
                            <img src="{{ asset('/images/background.png') }}" class="object-cover w-full h-full " alt="group image">
                        </div>

                        <h2 class="mt-2 text-lg font-semibold select-none">
                            Health & Wellness
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 select-none">
                            A supportive space to discuss health, wellness, and self-care tips.
                        </p>

                        <div class="flex items-center justify-between mt-3">

                            <span class="text-xs text-gray-500">
                                Members: 12
                            </span>

                            <a
                                class="text-sm font-medium text-blue-600 cursor-pointer hover:underline">
                                <i class="fa-solid fa-users"></i> Join Chat
                            </a>

                            {{-- <a href="{{ route('chat.groups.show', $group->id) }}"
                                class="text-sm font-medium text-blue-600 hover:underline">
                                Join Chat →
                            </a> --}}

                        </div>

                    </div>
                </div>
            </div>
              @endfor

        </div>

</div>
@endsection

@push('scripts')
    <script>

        {{--  const popupWindow = document.getElementById('popup-window');
        const xmarkBtn = document.getElementById('xmark-btn');
        const showGroup = document.getElementById('show-group');

        showGroup.addEventListener('click', ()=>{
            console.log('pressed');
            popupWindow.style.display = 'flex';
        });

        xmarkBtn.addEventListener('click', ()=>{
            popupWindow.style.display = 'none';
        });  --}}

    </script>
@endpush
