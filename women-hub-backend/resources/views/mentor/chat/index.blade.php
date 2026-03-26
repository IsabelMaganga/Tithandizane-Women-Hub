@extends('mentor.layouts.dashboard')
@section('title')
    chats
@endsection

@section('content')

    <div class=" h-[83vh] flex bg-white rounded shadow overflow-hidden">
       {{-- left chat list --}}
        <div class="w-1/4 bg-gray-100 border-b flex flex-col">
            <div class="p-4 border-b bg-[#111827] text-gray-100">
                <h1 class="text-lg font-bold">Chats</h1>
            </div>

            <div class="flex-1 overflow-y-auto">

                {{-- Chat items - loop through available chats --}}
                @for ($i = 0; $i < 10; $i++)

                <div class="p-4 border-b bg-white hover:bg-gray-200 cursor-pointer transition duration-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                        <p class="font-semibold">Isabel</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Last message: 12:00
                        </p>
                        </div>

                        <span class="bg-green-500 text-white text-xs rounded-full px-2 py-1">
                            1
                        </span>

                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-gray-500">
                        Status: active

                        </span>
                        </p>
                    </div>
                </div>

                @endfor


        </div>
    </div>


        {{-- right section --}}
        <div class=" flex-1 flex flex-col">
            {{-- header --}}
            <div class="div p-4 border-b flex items-center justify-between">
                <h1 class=" text-lg font-semibold capitalize">isabella mtengo</h1>
                <span class=" text-sm text-green-500">Online</span>
            </div>

            {{-- messages --}}
            <div id="chat_box" class="chatBox flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" >

                <div class="flex justify-start">
                    <div class=" max-w-xs md:max-w-md px-4 py-2 rounded-2xl shadow bg-blue-500 rounded-br-none text-white">
                       <p> Hi, isabel..... you're amazing</p>
                       <span class=" block text-xs mt-1 opacity-70">12:00:34</span>
                    </div>
                </div>
                <div id="message_box" class="flex justify-end">
                    <div class=" max-w-xs md:max-w-md px-4 py-2 rounded-2xl shadow bg-white rounded-bl-none text-gray-800">
                       <p> Aww! Thanks zouker for the complement</p>
                       <span class=" block text-xs mt-1 opacity-70">12:00:34</span>
                    </div>
                </div>

            </div>

            {{-- input --}}
            <form class=" p-0 border-t bg-white">
                <div class=" flex p-2 items-center space-x-2">
                    <input type="text" id="input" placeholder="Type your message..." class=" rounded-3xl flex-1 px-4 py-3 focus:outline-none focus:ring focus:ring-blue-500/20" @required(true)>
                    <button class=" bg-[#111827] rounded-3xl px-5 py-3 text-gray-100">Send</button>
                </div>
            </form>
        </div>

    </div>
    <script>
        const chatBox = document.getElementById('chat_box');
        const messageBox = document.getElementById('message_box');
        chatBox.scrollTop = chatBox.scrollHeight;

        function selectChat(chatId) {
            //redirect to the chat
            window.location.href = '/chat/' + chatId;
        }
    </script>
@endsection
