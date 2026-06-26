@php
    use Carbon\Carbon;

    $topicAccents = [
        'career'      => ['color' => '#4F6AF5', 'bg' => '#EEF0FF', 'text' => '#3B5BD9', 'icon' => 'ti-trending-up'],
        'leadership'  => ['color' => '#8B5CF6', 'bg' => '#F5F3FF', 'text' => '#6D28D9', 'icon' => 'ti-users'],
        'marketing'   => ['color' => '#EC4899', 'bg' => '#FDF2F8', 'text' => '#BE185D', 'icon' => 'ti-speakerphone'],
        'engineering' => ['color' => '#10B981', 'bg' => '#F0FDF4', 'text' => '#0F6E56', 'icon' => 'ti-code'],
        'health'      => ['color' => '#F59E0B', 'bg' => '#FFFBEB', 'text' => '#B45309', 'icon' => 'ti-heart'],
        'education'   => ['color' => '#06B6D4', 'bg' => '#ECFEFF', 'text' => '#0E7490', 'icon' => 'ti-book'],
        'menstrual'   => ['color' => '#E11D48', 'bg' => '#FFF1F2', 'text' => '#9F1239', 'icon' => 'ti-medical-cross'],
    ];

    $topic    = strtolower($session->topic ?? '');
    $accent   = collect($topicAccents)->first(fn($v, $k) => str_contains($topic, $k))
                ?? ['color' => '#4F6AF5', 'bg' => '#EEF0FF', 'text' => '#3B5BD9', 'icon' => 'ti-message'];

    $isLive   = $session->status === 'accepted' && $session->conversation_started_at;
    $statusKey = $isLive ? 'live' : $session->status;

    $statusMap = [
        'live'      => ['label' => 'Live',      'icon' => 'ti-radio',         'color' => '#991B1B', 'bg' => '#FEF2F2'],
        'pending'   => ['label' => 'Pending',   'icon' => 'ti-hourglass',     'color' => '#B45309', 'bg' => '#FFFBEB'],
        'accepted'  => ['label' => 'Accepted',  'icon' => 'ti-circle-check',  'color' => '#0F6E56', 'bg' => '#F0FDF4'],
        'declined'  => ['label' => 'Declined',  'icon' => 'ti-circle-x',      'color' => '#6B7280', 'bg' => '#F1F5F9'],
        'completed' => ['label' => 'Completed', 'icon' => 'ti-rosette',       'color' => '#1D4ED8', 'bg' => '#EEF0FF'],
        'missed'    => ['label' => 'Missed',    'icon' => 'ti-alert-circle',  'color' => '#991B1B', 'bg' => '#FEF2F2'],
    ];
    $badge    = $statusMap[$statusKey] ?? $statusMap['pending'];

    $menteeName  = $session->mentee->name ?? "Mentee #{$session->mentee_id}";
    $initials    = collect(explode(' ', $menteeName))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
    $canCancel   = in_array($session->status, ['pending', 'accepted']);
    $canStart    = $session->status === 'accepted';

    $date = $session->requested_date
        ? Carbon::parse($session->requested_date)->format('D, d M Y')
        : 'TBD';
    $timeFrom = $session->requested_time_from
        ? Carbon::parse($session->requested_time_from)->format('g:i A')
        : 'Pending';
    $duration = '';
    if ($session->requested_time_from && $session->requested_time_to) {
        $diff = Carbon::parse($session->requested_time_from)
                      ->diffInMinutes(Carbon::parse($session->requested_time_to));
        $duration = $diff > 0 ? "{$diff} min" : '';
    }
@endphp

<div class="session-card" style="
    display: flex;
    border-radius: 14px;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    overflow: hidden;
    min-height: 100%;
    {{ $isLive ? 'box-shadow: 0 3px 14px rgba(239,68,68,0.10);' : '' }}
">
    {{-- Accent bar --}}
    <div style="width: 5px; flex-shrink: 0; background: {{ $accent['color'] }};"></div>

    <div style="flex: 1; padding: 12px; min-width: 0;">

        {{-- Topic + Status badges --}}
        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 8px;">
            <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;background:{{ $accent['bg'] }};color:{{ $accent['text'] }};">
                <i class="ti {{ $accent['icon'] }}" aria-hidden="true"></i>
                {{ $session->topic }}
            </span>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                <i class="ti {{ $badge['icon'] }}" aria-hidden="true"></i>
                {{ $badge['label'] }}
            </span>
        </div>

        {{-- Title --}}
        <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);margin:0 0 4px;line-height:1.3;">
            {{ $session->topic ?? 'Mentorship Session' }}
        </h3>

        {{-- Message preview --}}
        @if($session->message)
            <p style="font-size:11px;color:var(--text-secondary);margin:0 0 8px;line-height:1.45;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                {{ $session->message }}
            </p>
        @endif

        {{-- Divider --}}
        <div style="height:1px;background:var(--border-color);margin:0 0 8px;"></div>

        {{-- Mentee row --}}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <div style="width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;background:{{ $accent['bg'] }};color:{{ $accent['text'] }};">
                {{ $initials }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:10px;color:var(--text-secondary);font-weight:500;margin:0 0 1px;">Mentee requesting</p>
                <p style="font-size:12px;font-weight:700;color:var(--text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $menteeName }}</p>
                @if($session->mentee?->email)
                    <p style="font-size:10px;color:var(--text-secondary);margin:0;">{{ $session->mentee->email }}</p>
                @endif
            </div>
            <i class="ti ti-user-circle" style="font-size:18px;color:var(--border-color);" aria-hidden="true"></i>
        </div>

        {{-- Schedule chips --}}
        <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px;">
            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:7px;font-size:10px;font-weight:600;background:var(--light-gray);color:var(--text-secondary);border:1px solid var(--border-color);">
                <i class="ti ti-calendar" style="font-size:11px;"></i> {{ $date }}
            </span>
            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:7px;font-size:10px;font-weight:600;background:var(--light-gray);color:var(--text-secondary);border:1px solid var(--border-color);">
                <i class="ti ti-clock" style="font-size:11px;"></i> {{ $timeFrom }}
            </span>
            @if($duration)
                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:7px;font-size:10px;font-weight:600;background:var(--light-gray);color:var(--text-secondary);border:1px solid var(--border-color);">
                    <i class="ti ti-hourglass" style="font-size:11px;"></i> {{ $duration }}
                </span>
            @endif
        </div>

        {{-- Action buttons --}}
        {{-- Action buttons --}}
<div style="display:flex;gap:8px;">

    @if($canCancel)
        <form action="{{ route('mentor.appointment.cancel', $session->id) }}" method="POST" style="flex:1;">
            @csrf @method('PATCH')
            <button type="submit"
                    onclick="return confirm('Cancel this session?')"
                    style="width:100%;padding:8px 0;border-radius:10px;border:1px solid var(--border-color);font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;background:transparent;color:var(--text-secondary);">
                <i class="ti ti-x" style="font-size:13px;"></i> Cancel
            </button>
        </form>
    @endif

    @if($canStart)
        {{-- If conversation already exists, go directly to it --}}
        <a href="{{ route('mentor.chat.session', $session->id) }}"
           style="flex:1.4;padding:8px 0;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;background:{{ $accent['color'] }};color:#fff;text-decoration:none;">
            <i class="ti {{ $isLive ? 'ti-login' : 'ti-player-play' }}" style="font-size:13px;"></i>
            {{ $isLive ? 'Rejoin session' : 'Start session' }}
        </a>
    @endif

    @if(!$canCancel && !$canStart)
        <div style="flex:1;padding:8px 0;border-radius:10px;background:var(--light-gray);font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);">
            {{ ucfirst($session->status) }}
        </div>
    @endif

</div>

    </div>
</div>