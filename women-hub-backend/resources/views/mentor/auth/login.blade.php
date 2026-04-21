<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Login - Tithandizane Women Hub</title>
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+DK+Uloopet+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        body{
            {{--  background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.5)), url('/images/background.png');  --}}
            background-attachment: fixed;
            background-size: cover;
            background-repeat: no-repeat;
            z-index: -1 ;
            font-family: "poppins", Arial, Helvetica, sans-serif;
        }
        /* .left-grid{
            transform: translateY(0 );
            animation: fadeInFromTop .9s forwards;
        }
        .right-section{
            transform: translateX(0 );
            animation: fadeIn .4s ease-in forwards ;
        } */

        @keyframes fadeIn {
             from {  opacity: 0; }
            to   {  opacity: 1; }
        }

        @keyframes fadeInFromTop {
            from { transform: translateY(-3000px);}
            to   { transform: translateY(0); }
        }

    </style>
</head>

<body class="grid items-center justify-center min-h-screen grid-cols-1 overflow-hidden bg-gray-100 md:grid-cols-3">

    {{-- bg-video --}}
    <div class="absolute top-0 left-0 z-0 w-full h-full overflow-hidden bg-video bg-amber-800">
        <video autoplay muted loop class="absolute top-0 left-0 object-cover w-full h-full">
            <source src=" {{  asset('/videos/c509ded08783fbf26a1eaf4592cb414af13df134.mp4') }}" type="video/mp4">
        </video>
    </div>

    {{-- overlay --}}
    <div class="absolute inset-0 z-10 w-full h-full overflow-hidden overlay bg-black/40"></div>

    {{-- left-grid container --}}
    <div class="z-10 flex flex-col items-center justify-between w-full min-h-screen gap-20 px-6 pt-4 pb-10 bg-white left-grid">

        {{-- top-left-details-section --}}
        <div class="flex items-center justify-start w-full gap-3 mt-1 top">

            <div class="user-avator w-[60px]  contain-content  h-[60px] bg-[#962980] text-4xl hover:bg-gray-100/0 cursor-pointer transition-all p-1 rounded-full flex items-center justify-center ">
                <img src=" {{ asset('/logo.png') }}" alt="Mentor Avatar" class="object-cover w-full h-full rounded-full hover:rotate-3">
            </div>

            <div class="text-left ">
                <h1 class="text-2xl font-bold text-gray-800">Mentor Login</h1>
                <div class="">
                    <p class="text-sm text-gray-600 text-shadow-2xl text-shadow-black">
                        Mentor Portal - Tithandizane Women Hub
                    </p>
                </div>
            </div>

            {{--  top circle buttons  --}}
            <div class="absolute grid grid-cols-3 gap-2 dots right-4 top-4">
                <p class=" bg-[#962980] w-5 h-5 rounded-full"></p>
                <p class=" bg-[#962980]/70 w-5 h-5 rounded-full"></p>
                <p class=" bg-[#db3fbc] w-5 h-5 rounded-full"></p>
            </div>


        </div>

        <div class="w-full py-4 rounded-r-lg px-7">

            <div class="flex flex-row items-center justify-center w-full gap-2 login-top-section">

                {{-- login-top-user-banner-avat --}}
                <div class="user-avator w-[90px] p-2 h-[90px] bg-[#962980] text-5xl hover:bg-[#bd36a2] cursor-pointer transition-all  rounded-full flex items-center justify-center ">
                    <i class="fa-regular fa-user font-light  text-[#ffffff]"></i>
                </div>

            </div>

            {{-- successful message --}}
            @if(session('success'))
            <div id="success" class="p-4 mt-2 mb-0 rounded bg-green-50">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if ($errors->any())
                <div id="error" class="px-4 py-2 mt-2 mb-0 text-sm text-red-500 bg-red-100 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>

                    @endforeach
                </div>
            @endif

            <form id="signinForm" method="POST" action="{{ route('mentor.login.post') }}" class="w-full ">
                @csrf

                {{-- email --}}
                <div class="mt-5 mb-5">

                    <div class="relative input-field">

                        <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="input w-full px-3 pl-10 py-3 text-sm placeholder:text-sm placeholder:text-[#962980]/50 text-[#962980] transition-all ease-in-out border-2 bg-amber-50/0 border-[#962980]/30 rounded-3xl focus:outline-none focus:ring-2 focus:ring-[#962980] focus:border-transparent"
                        placeholder="mentor@tithandizane.mw"
                        required
                        autocomplete="email"
                        autofocus
                        >

                        <i class="fa-regular fa-envelope absolute left-3 top-3 text-[#962980]/80 text-xl"></i>
                    </div>
                </div>

                {{-- password --}}
                <div class="mb-5">

                    <div class="relative input-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="input w-full px-3 pl-10 text-sm placeholder:text-sm placeholder:text-[#962980]/30 text-[#962980] transition-all ease-in-out py-3 border-2 bg-amber-50/0 border-[#962980]/30 rounded-3xl focus:outline-none focus:ring-2 focus:ring-[#962980] focus:border-transparent"
                            placeholder="xkdo9@SRS!f.."
                            required
                            autocomplete="current-password"
                        >

                        <i class="fa-solid fa-lock absolute left-3 top-3 text-[#962980]/80 text-xl"></i>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-3 mb-4 forgot-password">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember" class="form-checkbox accent-[#962980] h-4 w-4 text-[#962980] focus:ring-[#962980] border-gray-300 rounded">
                        <span class="text-sm text-gray-600 select-none">Remember me</span>
                    </label>

                     <label class="flex items-center gap-2">
                        <a href="{{  route('mentor.forgot') }}" class="text-sm text-gray-600 select-none">forgot password</a>
                    </label>
                </div>

                    <button
                        type="submit"
                        class="w-full bg-[#962980] active:bg-[#5c1a4f] hover:bg-[#af2a95] border border-[#eeeeec]/20 transition delay-75 px-5 py-4 rounded-3xl text-white text-sm font-semibold leading-normal flex items-center justify-center gap-2"
                    >
                        Sign In
                    </button>

            </form>

        </div>

    </div>

    {{-- right-section --}}
    <div class="z-10 flex items-end justify-end w-full col-span-2 gap-20 p-0 mx-auto right-section min-h-scree">

        <div class="flex flex-col items-end justify-end w-full min-h-screen p-5 ">
            <div class="text-right ">
                <h1 class="mb-4 font-extrabold tracking-wide text-white text-7xl">Welcome!</h1>
                <p class="pl-5 text-sm text-gray-50">Empower and Inspire: Connect with passionate mentees, share your wisdom and experiences, foster growth through meaningful guidance, and make a lasting impact in the lives of women at Tithandizane.    </p>

                <div class="flex items-center justify-end w-full gap-5 mt-10 mb-5 text-sm text-gray-200">
                    <p> <a href="{{ route('get.started') }}"  class=" text-[#d3d2d2] hover:text-white transition">tithandizane@help.com</a></p>
                    <p> <a href="{{ route('get.started') }}"  class=" text-[#d3d2d2] hover:text-white transition ">Back to portal selection</a></p>
                </div>
            </div>
        </div>

    </div>


   <script>

    let signinForm = document.getElementById('signinForm');

    signinForm.addEventListener('submit', function(e) {
        alert(200);
    });

        window.addEventListener('DOMContentLoaded', ()=>{

            const msgSuccess = document.getElementById('success');
            const msgError = document.getElementById('error');

            if(msgSuccess){
                msgSuccess.style.display = 'flex';
                setTimeout(() => {
                    msgSuccess.style.display = 'none';
                }, 5000);
            }

              if(msgError){
                msgError.style.display = 'flex';
                setTimeout(() => {
                    msgError.style.display = 'none';
                    msgError.style.transition = '.2s ease-in-out';
                }, 5000);
            }

        })

</script>


</body>
</html>
