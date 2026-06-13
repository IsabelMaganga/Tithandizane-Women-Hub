@extends('mentor.layouts.dashboard')

@section('title')
    Assigned Task Details
@endsection

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold">Task Details</h1>
            <p class="mt-2 text-sm text-gray-500">Review the assigned report and follow the steps below to handle it.</p>
        </div>
        <a href="{{ route('mentor.assigned.reports') }}" class="px-4 py-2 text-sm font-semibold text-white bg-gray-800 rounded-lg hover:bg-gray-900">Back to assigned tasks</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="p-6 bg-white rounded-2xl shadow-sm lg:col-span-2">
            <div class="mb-6">
                <h2 class="text-xl font-semibold">{{ $report->incident_title }}</h2>
                <p class="mt-2 text-sm text-gray-500">Reference: <span class="font-medium">{{ $report->reference_number }}</span></p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Type</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ ucfirst($report->incident_type) }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ ucfirst($report->status) }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Submitted</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $report->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Victim</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $report->submitter_name }}</p>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold">Incident description</h3>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{{ $report->incident_description }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Location</h3>
                    <p class="mt-2 text-sm text-gray-700">{{ $report->incident_location ?? 'Not provided' }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Perpetrator information</h3>
                    <p class="mt-2 text-sm text-gray-700">{{ $report->perpetrator_info ?? 'Not provided' }}</p>
                </div>
                @if(!$report->is_anonymous)
                <div>
                    <h3 class="text-lg font-semibold">Victim contact</h3>
                    <p class="mt-2 text-sm text-gray-700">{{ $report->submitter_email ?? $report->victim_email ?? 'Not available' }}</p>
                    <p class="mt-2 text-sm text-gray-700">{{ $report->victim_phone ?? 'Not available' }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6 lg:col-span-1">
            <div class="p-6 bg-white rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold">How to handle</h3>
                <ol class="mt-4 space-y-3 text-sm text-gray-700 list-decimal list-inside">
                    <li>Read the incident details and confirm the victim’s needs.</li>
                    <li>Review the report type and any supporting information.</li>
                    <li>Reach out to the victim if contact details are available and if it is safe.</li>
                    <li>Provide guidance, support, or next steps based on your expertise.</li>
                    <li>Update the admin or resolve the report following the system process.</li>
                </ol>
            </div>

            <div class="p-6 bg-white rounded-2xl shadow-sm">
                <h3 class="text-lg font-semibold">Quick actions</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('mentor.notifications') }}" class="block px-4 py-3 text-center text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">Go to notifications</a>
                    <a href="{{ route('mentor.assigned.reports') }}" class="block px-4 py-3 text-center text-sm font-semibold text-gray-900 bg-gray-100 rounded-lg hover:bg-gray-200">Back to assigned tasks</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection