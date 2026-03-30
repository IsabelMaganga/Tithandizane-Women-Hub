@push('styles')
    <style>
        .construction {
            color: #4B5563; /* Tailwind's gray-700 */
        }
        .fa-solid{
            animation: hammering 5s linear infinite;
        }
        @keyframes hammering {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(35deg); }
            50% { transform: rotate(0deg); }
            75% { transform: rotate(-35deg); }
            100% { transform: rotate(0deg); }
        }

    </style>
@endpush

@section('content')

    <div class="flex flex-col items-center justify-center w-full min-h-full">

        <div class="construction text-center">
            <i class="fa-solid fa-hammer mt-10 text-9xl"></i>
            <h1  class=" text-5xl mb-2 mt-5 capitalize">page under construction</h1>
            <h1  class=" text-2xl capitalize">will be added soon</h1>
        </div>
    </div>

@endsection
