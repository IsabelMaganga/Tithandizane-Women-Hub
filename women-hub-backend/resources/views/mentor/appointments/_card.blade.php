<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-4 shadow-sm hover:shadow-md transition">
    <div class="flex items-start justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $session->topic ?? 'Mentorship Session' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Mentee: <span class="font-medium text-slate-700">{{ $session->mentee->name ?? 'Unknown' }}</span></p>
        </div>
        <span class="px-3 py-1 text-xs rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 font-bold uppercase tracking-wider">
            {{ ucfirst($session->status) }}
        </span>
    </div>
    <div class="mt-6 flex items-center justify-between">
        <div class="flex gap-6 text-sm text-slate-500">
            <p>📅 {{ $session->scheduled_at?->format('d M Y') ?? 'TBD' }}</p>
            <p>⏰ {{ $session->scheduled_at?->format('h:i A') ?? 'Pending' }}</p>
        </div>
    </div>
</div>