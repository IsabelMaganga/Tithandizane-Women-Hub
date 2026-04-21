@extends('mentor.layouts.dashboard')
@section('title')
    Report
@endsection

@section('content')
      <div class="grid w-full h-full max-w-full grid-cols-3 gap-10 p-6 mx-auto ">
        <div class="w-full col-span-3 header">
            <h1 class="text-4xl font-semibold">Reported Issues</h1>
        </div>


        @if($reports->isEmpty())
            <p class="text-gray-600">No reports have been submitted yet.</p>
        @else



            <div class="col-span-2 p-4 bg-white shadow rounded-2xl">
                 @if(session('success'))
                    <div id="success" class="p-3 mt-2 mb-4 text-green-700 bg-green-100 rounded success">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full text-sm border border-collapse border-gray-300">
                    <thead>
                        <tr class="text-left bg-gray-100">
                            <th class="p-2 border">ID</th>
                            <th class="p-2 border">Username</th>
                            <th class="p-2 border">Title</th>
                            <th class="p-2 border">Type</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Issue Date</th>
                            <th class="p-2 border">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="p-2 border">{{ $report->id }}</td>
                                <td class="p-2 border">{{ $report->username }}</td>
                                <td class="p-2 border">{{ $report->title }}</td>
                                <td class="p-2 border">{{ ucfirst($report->type) }}</td>
                                <td class="p-2 border">{{ ucfirst($report->status) }}</td>
                                <td class="p-2 border">
                                    {{ \Carbon\Carbon::parse($report->issue_date)->format('Y-m-d H:i') }}
                                </td>
                                <td class="p-2 border">
                                    {{ $report->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-500">
                                    No reports found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


         @endif

        <div class="flex flex-col justify-start right-section">
             <div class="transition-all delay-300">
                <img src="{{  asset('/images/vecteezy_3d-illustration-of-a-teenage-female-programmer-at-the_35899074.png') }}" alt="">
            </div>
        </div>

        <div class="flex items-center justify-between w-full col-span-3 text-sm header">
             <a href="{{  route('mentor.reports') }}">Create another issue</a>

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
            alert(200);

            const msgBox = document.getElementById('success');
            if(msgBox){
                 msgBox.style.display = 'flex';
                setTimeout(()=>{
                    msgBox.style.display = 'none';
                },3000);
            }
        })
    </script>
@endpush
