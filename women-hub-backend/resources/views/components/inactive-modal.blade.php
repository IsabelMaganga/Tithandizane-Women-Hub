{{-- ============================================================
     Inactive Account Modal
     Usage: @include('components.inactive-modal')
     Trigger: add class "open" to #inactiveModal via JS when
              session('account_inactive') is true
     ============================================================ --}}

<style>
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

    #inactiveModal.open {
        display: flex;
        animation: modalFadeIn 0.25s ease-out forwards;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .inactive-modal-card {
        animation: modalSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes modalSlideUp {
        from { transform: translateY(40px) scale(0.95); opacity: 0; }
        to   { transform: translateY(0)    scale(1);    opacity: 1; }
    }

    .icon-pulse-ring {
        animation: pulseRing 2s ease-out infinite;
    }

    @keyframes pulseRing {
        0%   { box-shadow: 0 0 0 0    rgba(150, 41, 128, 0.40); }
        70%  { box-shadow: 0 0 0 16px rgba(150, 41, 128, 0);    }
        100% { box-shadow: 0 0 0 0    rgba(150, 41, 128, 0);    }
    }
</style>


<div id="inactiveModal" role="dialog" aria-modal="true" aria-labelledby="inactiveModalTitle">
    <div class="inactive-modal-card bg-white rounded-3xl shadow-2xl w-[90%] max-w-sm mx-auto p-8 text-center">

        {{-- Pulsing lock icon --}}
        <div class="icon-pulse-ring w-20 h-20 rounded-full bg-[#962980]/10 flex items-center justify-center mx-auto mb-2">
            <div class="w-14 h-14 rounded-full bg-[#962980]/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#962980]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>

        {{-- Status badge --}}
        <span class="inline-block mt-3 mb-4 px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600 tracking-wide uppercase">
            Account Deactivated
        </span>

        <h2 id="inactiveModalTitle" class="text-xl font-bold text-gray-800 mb-3">
            Access Restricted
        </h2>

        <p class="text-sm text-gray-500 leading-relaxed mb-6">
            Your account has been deactivated and you are currently unable to access the portal.
            Please contact the administrator to have your account reinstated.
        </p>

        {{-- Divider --}}
        <div class="border-t border-gray-100 mb-6"></div>

        {{-- Contact card --}}
        <div class="flex items-center gap-3 bg-[#962980]/5 border border-[#962980]/20 rounded-2xl px-4 py-3 mb-6 text-left">
            <div class="w-10 h-10 rounded-full bg-[#962980] flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Contact administrator</p>
                <a href="mailto:info@tithandizane.mw"
                   class="text-sm font-semibold text-[#962980] hover:text-[#5c1a4f] transition-colors">
                    info@tithandizane.mw
                </a>
            </div>
        </div>

        {{-- Phone contact --}}
        <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 mb-6 text-left">
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Call us</p>
                <a href="tel:+265000000000"
                   class="text-sm font-semibold text-gray-700 hover:text-[#962980] transition-colors">
                    +265 984 946 206
                </a>
            </div>
        </div>

        {{-- Close button --}}
        <button
            id="closeInactiveModal"
            class="w-full py-3 rounded-2xl text-sm font-semibold text-white
                   bg-[#962980] hover:bg-[#af2a95] active:bg-[#5c1a4f]
                   transition-colors duration-200"
        >
            Got it, I'll reach out
        </button>

        <p class="mt-4 text-xs text-gray-400">
            Already resolved?
            <a href="{{ url()->current() }}" class="text-[#962980] hover:underline font-medium">Refresh and try again</a>
        </p>

    </div>
</div>


<script>
    (function () {
        const modal    = document.getElementById('inactiveModal');
        const closeBtn = document.getElementById('closeInactiveModal');

        function openModal()  { modal.classList.add('open');    }
        function closeModal() { modal.classList.remove('open'); }

        // Auto-open if session flag is set
        const shouldOpen = @json(session('account_inactive') === true);
        if (shouldOpen) openModal();

        // Close on button
        closeBtn.addEventListener('click', closeModal);

        // Close on backdrop click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    })();
</script>