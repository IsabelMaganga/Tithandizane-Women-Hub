@extends('mentor.layouts.dashboard')

@section('title')
    Assigned Tasks
@endsection

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold">Assigned Tasks</h1>
            <p class="mt-2 text-sm text-gray-500">These are the harassment reports assigned to you by the admin.</p>
        </div>
        <a href="{{ route('mentor.notifications') }}" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">View notifications</a>
    </div>

    @if($reports->isEmpty())
        <div class="p-6 bg-white rounded-2xl shadow-sm">
            <p class="text-gray-600">You have no assigned tasks at the moment.</p>
        </div>
    @else
        <div class="overflow-x-auto bg-white rounded-2xl shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report->reference_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $report->incident_title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($report->incident_type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($report->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $report->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('mentor.assigned.reports.show', $report->id) }}" class="text-blue-600 hover:text-blue-800">View details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $reports->links() }}</div>
    @endif
</div>
@endsection