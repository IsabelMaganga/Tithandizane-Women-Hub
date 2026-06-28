<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Login - Tithandizane Women Hub</title>
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        body {
            background-attachment: fixed;
            background-size: cover;
            background-repeat: no-repeat;
            z-index: -1;
            font-family: "Poppins", Arial, Helvetica, sans-serif;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .btn-spinning {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .animate-fade-in {
            animation: fadeInUp 0.2s ease-out forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Inactive account modal ───────────────────────────────── */
        #inactiveModal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        /* ✅ FIXED: explicitly set display:flex so the modal actually appears */
        #inactiveModal.open {
            display: flex;
            animation: modalFadeIn 0.25s ease-out forwards;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .modal-card {
            animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes modalSlideUp {
            from { transform: translateY(40px) scale(0.96); opacity: 0; }
            to   { transform: translateY(0)    scale(1);    opacity: 1; }
        }

        /* pulse ring behind the icon */
        .icon-ring {
            animation: pulse-ring 2s ease-out infinite;
        }

        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0   rgba(150, 41, 128, 0.35); }
            70%  { box-shadow: 0 0 0 14px rgba(150, 41, 128, 0);   }
            100% { box-shadow: 0 0 0 0   rgba(150, 41, 128, 0);    }
        }

    </style>
</head>

<body class="grid items-center justify-center min-h-screen grid-cols-1 overflow-hidden bg-gray-100 md:grid-cols-3">

    {{-- bg-video --}}
    <div class="absolute top-0 left-0 z-0 w-full h-full overflow-hidden bg-video bg-amber-800">
        <video autoplay muted loop class="absolute top-0 left-0 object-cover w-full h-full">
            <source src="{{ asset('/videos/c509ded08783fbf26a1eaf4592cb414af13df134.mp4') }}" type="video/mp4">
        </video>
    </div>

    {{-- overlay --}}
    <div class="absolute inset-0 z-10 w-full h-full overflow-hidden overlay bg-black/40"></div>

    {{-- left-grid container --}}
    <div class="z-10 flex flex-col items-center justify-between w-full min-h-screen gap-20 px-6 pt-4 pb-10 bg-white animate-fade-in left-grid">

        {{-- top-left-details-section --}}
        <div class="flex items-center justify-start w-full gap-3 mt-1 top">

            <div class="user-avator w-[60px] contain-content h-[60px] bg-[#962980] text-4xl hover:bg-gray-100/0 cursor-pointer transition-all p-1 rounded-full flex items-center justify-center">
                <img src="{{ asset('/logo.png') }}" alt="Mentor Avatar" class="object-cover w-full h-full rounded-full hover:rotate-3">
            </div>

            <div class="text-left">
                <h1 class="text-2xl font-bold text-gray-800">Mentor Login</h1>
                <div class="">
                    <p class="text-sm text-gray-600">
                        Mentor Portal - Tithandizane Women Hub
                    </p>
                </div>
            </div>

            {{-- top circle buttons --}}
            <div class="absolute grid grid-cols-3 gap-2 dots right-4 top-4">
                <p class="bg-[#962980] w-5 h-5 rounded-full"></p>
                <p class="bg-[#962980]/70 w-5 h-5 rounded-full"></p>
                <p class="bg-[#db3fbc] w-5 h-5 rounded-full"></p>
            </div>

        </div>

        <div class="w-full py-4 rounded-r-lg px-7">

            <div class="flex flex-row items-center justify-center w-full gap-2 login-top-section">
                <div class="user-avator w-[90px] p-2 h-[90px] bg-[#962980] text-5xl hover:bg-[#bd36a2] cursor-pointer transition-all rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-user font-light text-[#ffffff]"></i>
                </div>
            </div>

            {{-- success message --}}
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

            <form id="signinForm" method="POST" action="{{ route('mentor.login') }}" class="w-full">
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
                            autocomplete="email">
                        <i class="fa-regular fa-envelope absolute left-3 top-3 text-[#5c1a4fb2]/70 text-xl"></i>
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
                            autocomplete="current-password">
                        <i class="fa-solid fa-lock absolute left-3 top-3 text-[#5c1a4fb2]/70 text-xl"></i>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-3 mb-4 forgot-password">
                    <label class="flex items-center gap-2">
                        <a href="#" class="text-sm text-[#5c1a4fb2] hover:text-[#5c1a4f] transition-all select-none">forgot password</a>
                    </label>
                </div>

                <button
                    id="signInBtn"
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
        <div class="flex flex-col items-end justify-end w-full min-h-screen p-5">
            <div class="text-right">
                <h1 class="mb-4 font-extrabold tracking-wide text-white text-7xl">Welcome!</h1>
                <p class="pl-5 text-sm text-gray-50">Empower and Inspire: Connect with passionate mentees, share your wisdom and experiences, foster growth through meaningful guidance, and make a lasting impact in the lives of women at Tithandizane.</p>

                <div class="flex items-center justify-end w-full gap-5 mt-10 mb-5 text-sm text-gray-200">
                    <p><a class="text-[#d3d2d2] hover:text-[#ff00cc] transition">tithandizane@help.com</a></p>
                    <p><a href="{{ route('get.started') }}" class="text-[#d3d2d2] hover:text-[#ff00cc] transition">Back to portal selection</a></p>
                </div>
            </div>
        </div>
    </div>


    {{-- ── Inactive-account modal ─────────────────────────────────────── --}}
    <div id="inactiveModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card bg-white rounded-2xl shadow-2xl w-[90%] max-w-sm mx-auto p-8 text-center">

            {{-- pulsing lock icon --}}
            <div class="icon-ring w-20 h-20 rounded-full bg-[#962980]/10 flex items-center justify-center mx-auto mb-5">
                <div class="w-14 h-14 rounded-full bg-[#962980]/15 flex items-center justify-center">
                    <i class="fa-solid fa-lock text-[#962980] text-2xl"></i>
                </div>
            </div>

            <h2 id="modalTitle" class="text-xl font-bold text-gray-800 mb-2">Account Deactivated</h2>

            <p class="text-sm text-gray-500 leading-relaxed mb-6">
                Your account has been deactivated and you cannot access the mentor portal right now.
                Please reach out to the administrator to have it reinstated.
            </p>

            {{-- contact card --}}
            <div class="flex items-center gap-3 bg-[#962980]/8 border border-[#962980]/20 rounded-xl px-4 py-3 mb-6 text-left">
                <div class="w-9 h-9 rounded-full bg-[#962980] flex items-center justify-center flex-shrink-0">
                    <i class="fa-regular fa-envelope text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 leading-none mb-0.5">Contact administrator</p>
                    <a href="mailto:info@tithandizane.mw"
                       class="text-sm font-semibold text-[#962980] hover:text-[#5c1a4f] transition-colors">
                        info@tithandizane.mw
                    </a>
                </div>
            </div>

            <button
                id="closeInactiveModal"
                class="w-full py-3 rounded-2xl text-sm font-semibold text-white bg-[#962980] hover:bg-[#af2a95] active:bg-[#5c1a4f] transition-colors"
            >
                Got it
            </button>

        </div>
    </div>
    {{-- ── /Inactive-account modal ──────────────────────────────────────── --}}


    <script>

        // ── Sign-in spinner ────────────────────────────────────────────
        const signinForm = document.getElementById('signinForm');
        signinForm.addEventListener('submit', function () {
            const signInBtn = document.getElementById('signInBtn');
            signInBtn.innerHTML = `
                <p class='w-5 h-5 border-l-2 border-white rounded-full btn-spinning'></p>
                <p>signing in...</p>`;
        });

        // ── All DOM-dependent logic runs after load ────────────────────
        window.addEventListener('DOMContentLoaded', () => {

            // ── Auto-hide flash messages ───────────────────────────────
            const msgSuccess = document.getElementById('success');
            const msgError   = document.getElementById('error');

            if (msgSuccess) {
                setTimeout(() => { msgSuccess.style.display = 'none'; }, 5000);
            }

            if (msgError) {
                setTimeout(() => {
                    msgError.style.transition = '.2s ease-in-out';
                    msgError.style.display    = 'none';
                }, 5000);
            }

            // ── Inactive-account modal ─────────────────────────────────
            // ✅ FIXED: this value is now correctly populated because
            // showLogin() renders the view directly (no double redirect).
            const accountInactive = @json(session('account_inactive') === true);

            if (accountInactive) {
                document.getElementById('inactiveModal').classList.add('open');
            }

            const modal      = document.getElementById('inactiveModal');
            const closeBtn   = document.getElementById('closeInactiveModal');

            function closeModal() {
                modal.classList.remove('open');
            }

            // Close on button click
            closeBtn.addEventListener('click', closeModal);

            // Close on backdrop click
            modal.addEventListener('click', function (e) {
                if (e.target === this) closeModal();
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

        });

    </script>

</body>
</html>