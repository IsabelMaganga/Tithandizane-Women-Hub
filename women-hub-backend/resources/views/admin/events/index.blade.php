{{-- admin.events.calendar --}}
@extends('admin.layouts.admin')

@section('title', 'Events Calendar')
@section('page-title', 'Events Calendar')
@section('page-subtitle', 'Manage and view all events, training sessions, and workshops')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet'>
    <style>
        /* ── Reset / Variables ── */
        :root {
            --radius-card: 20px;
            --radius-btn: 12px;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.04);
            --transition: 0.2s ease-in-out;
        }

        /* ── Calendar card ── */
        .calendar-wrap {
            background: var(--card-bg);
            border-radius: var(--radius-card);
            border: 1px solid var(--border-color);
            padding: 28px 24px 24px;
            box-shadow: var(--shadow-card);
            transition: box-shadow var(--transition);
        }

        /* ── Toolbar ── */
        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
            gap: 0.75rem 0.5rem;
        }

        .fc .fc-toolbar-title {
            color: var(--text-primary);
            font-size: 1.4rem !important;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .fc .fc-button {
            background-color: var(--purple) !important;
            border: none !important;
            color: #fff !important;
            border-radius: var(--radius-btn) !important;
            padding: 0.5rem 1rem !important;
            font-weight: 500 !important;
            text-transform: capitalize !important;
            box-shadow: 0 2px 6px rgba(120, 80, 220, 0.20) !important;
            transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
        }

        .fc .fc-button:hover {
            background-color: var(--purple-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(120, 80, 220, 0.30) !important;
        }

        .fc .fc-button-primary:disabled {
            opacity: 0.5 !important;
            pointer-events: none;
        }

        .fc .fc-button-group .fc-button {
            border-radius: 0 !important;
        }
        .fc .fc-button-group .fc-button:first-child {
            border-radius: var(--radius-btn) 0 0 var(--radius-btn) !important;
        }
        .fc .fc-button-group .fc-button:last-child {
            border-radius: 0 var(--radius-btn) var(--radius-btn) 0 !important;
        }

        /* ── Day grid ── */
        .fc .fc-daygrid-day-frame {
            background: var(--card-bg);
            transition: background var(--transition);
        }
        .fc .fc-daygrid-day-frame:hover {
            background: var(--bg-secondary);
        }

        .fc .fc-daygrid-day-number {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .fc .fc-col-header-cell-cushion {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .fc .fc-day-other .fc-daygrid-day-number {
            color: var(--text-muted);
        }

        /* ── Events ── */
        .fc .fc-event {
            border-radius: 10px !important;
            padding: 4px 10px !important;
            font-size: 0.8rem;
            font-weight: 500;
            border: none !important;
            cursor: pointer;
            transition: transform var(--transition), box-shadow var(--transition);
            margin: 2px 4px;
        }

        .fc .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .fc .fc-daygrid-event-harness {
            margin: 2px 0;
        }

        /* ── Modal ── */
        .event-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1050;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .event-modal.show {
            display: flex;
        }

        .event-modal-content {
            background: var(--card-bg);
            padding: 2rem 2rem 1.8rem;
            border-radius: 24px;
            max-width: 520px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            animation: modalFadeIn 0.25s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(8px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .event-modal-content .close-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            line-height: 1;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: background var(--transition), color var(--transition);
        }

        .event-modal-content .close-btn:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        /* ── Badges ── */
        .event-type-badge {
            display: inline-block;
            padding: 0.2rem 0.9rem;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .event-type-training {
            background: var(--light-blue);
            color: var(--blue);
        }
        .event-type-workshop {
            background: var(--light-teal);
            color: var(--teal-green);
        }
        .event-type-meeting {
            background: var(--light-orange);
            color: var(--orange);
        }
        .event-type-general {
            background: var(--light-purple);
            color: var(--purple);
        }

        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.9rem;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .status-upcoming {
            background: var(--light-blue);
            color: var(--blue);
        }
        .status-ongoing {
            background: var(--light-green);
            color: var(--teal-green);
        }
        .status-completed {
            background: var(--light-gray);
            color: var(--text-secondary);
        }
        .status-cancelled {
            background: var(--light-red);
            color: var(--red);
        }

        /* ── Modal content ── */
        .modal-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 0.75rem;
            margin-bottom: 1.25rem;
        }

        .modal-field {
            margin-bottom: 1rem;
        }
        .modal-field label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secondary);
            margin-bottom: 0.2rem;
        }
        .modal-field .value {
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-primary);
            word-break: break-word;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.8rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .btn-edit {
            background: var(--blue);
            color: #fff;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: var(--radius-btn);
            font-weight: 600;
            font-size: 0.85rem;
            transition: background var(--transition), transform var(--transition);
        }
        .btn-edit:hover {
            background: var(--blue-dark);
            transform: translateY(-1px);
        }

        .btn-delete {
            background: var(--red);
            color: #fff;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: var(--radius-btn);
            font-weight: 600;
            font-size: 0.85rem;
            transition: background var(--transition), transform var(--transition);
        }
        .btn-delete:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
        }

        /* ── Responsive tweaks ── */
        @media (max-width: 640px) {
            .calendar-wrap {
                padding: 16px 12px;
            }
            .fc .fc-toolbar-title {
                font-size: 1.1rem !important;
            }
            .fc .fc-button {
                padding: 0.35rem 0.7rem !important;
                font-size: 0.8rem !important;
            }
            .event-modal-content {
                padding: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Events Calendar</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-secondary);">
                Manage and view all events, training sessions, and workshops
            </p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-semibold text-sm transition-all hover:opacity-90 hover:translate-y-[-1px] shadow-md"
           style="background: var(--purple); box-shadow: 0 4px 12px rgba(120,80,220,0.25);">
            <i class="fas fa-plus-circle"></i> Create Event
        </a>
    </div>

    <!-- Calendar -->
    <div class="calendar-wrap">
        <div id="calendar"></div>
    </div>

    <!-- Event Details Modal -->
    <div id="eventModal" class="event-modal">
        <div class="event-modal-content">
            <div class="flex items-start justify-between mb-3">
                <h2 id="modalTitle" class="text-xl font-bold leading-tight" style="color: var(--text-primary);"></h2>
                <button class="close-btn" onclick="closeModal()" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="modalContent"></div>

            <div class="modal-actions">
                <button id="editEventBtn" class="btn-edit">
                    <i class="fas fa-pen me-2"></i> Edit
                </button>
                <button id="deleteEventBtn" class="btn-delete">
                    <i class="fas fa-trash-alt me-2"></i> Delete
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'standard',
                height: 'auto',
                events: '{{ route("admin.events.data") }}',
                eventClick: function(info) {
                    showEventDetails(info.event);
                },
                editable: false,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
            });

            calendar.render();
            window.calendar = calendar;
        });

        // ── Modal logic ──
        let currentEventId = null;

        function showEventDetails(event) {
            currentEventId = event.id;
            const props = event.extendedProps || {};

            document.getElementById('modalTitle').textContent = event.title || 'Event';

            // Type badge
            const typeMap = {
                training: 'event-type-training',
                workshop: 'event-type-workshop',
                meeting: 'event-type-meeting',
            };
            const typeClass = typeMap[props.type] || 'event-type-general';
            const typeLabel = props.type ? props.type.charAt(0).toUpperCase() + props.type.slice(1) : 'General';

            // Status badge
            const statusMap = {
                upcoming: 'status-upcoming',
                ongoing: 'status-ongoing',
                completed: 'status-completed',
                cancelled: 'status-cancelled',
            };
            const statusClass = statusMap[props.status] || 'status-upcoming';
            const statusLabel = props.status ? props.status.charAt(0).toUpperCase() + props.status.slice(1) : 'Upcoming';

            // Build meta badges
            const metaHtml = `
                <span class="event-type-badge ${typeClass}">${typeLabel}</span>
                <span class="status-badge ${statusClass}">${statusLabel}</span>
            `;

            // Date & time
            let dateStr = '—';
            let timeStr = '';
            if (event.start) {
                const d = new Date(event.start);
                dateStr = d.toLocaleDateString(undefined, {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                timeStr = d.toLocaleTimeString(undefined, {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                if (event.end) {
                    const e = new Date(event.end);
                    timeStr += ' – ' + e.toLocaleTimeString(undefined, {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }

            // Location
            const location = props.location || null;

            // Description
            const description = props.description || null;

            // Participants
            const maxParticipants = props.max_participants || null;
            let participantsHtml = '';
            if (maxParticipants) {
                const current = props.current_participants || 0;
                participantsHtml = `
                    <div class="modal-field">
                        <label>Participants</label>
                        <div class="value">${current} / ${maxParticipants}</div>
                    </div>
                `;
            }

            const contentHtml = `
                <div class="modal-meta">${metaHtml}</div>

                <div class="modal-field">
                    <label>Date &amp; Time</label>
                    <div class="value">${dateStr} · ${timeStr}</div>
                </div>

                ${location ? `
                    <div class="modal-field">
                        <label>Location</label>
                        <div class="value">${location}</div>
                    </div>
                ` : ''}

                ${description ? `
                    <div class="modal-field">
                        <label>Description</label>
                        <div class="value" style="font-weight:400; white-space:pre-wrap;">${description}</div>
                    </div>
                ` : ''}

                ${participantsHtml}
            `;

            document.getElementById('modalContent').innerHTML = contentHtml;
            document.getElementById('eventModal').classList.add('show');

            // ── Action buttons ──
            document.getElementById('editEventBtn').onclick = function() {
                if (currentEventId) {
                    window.location.href = '/admin/events/' + currentEventId + '/edit';
                }
            };

            document.getElementById('deleteEventBtn').onclick = function() {
                if (!currentEventId) return;
                if (confirm('Delete this event permanently?')) {
                    deleteEvent(currentEventId);
                }
            };
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('show');
            currentEventId = null;
        }

        function deleteEvent(eventId) {
            fetch('/admin/events/' + eventId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || !data.error) {
                        closeModal();
                        if (window.calendar) window.calendar.refetchEvents();
                        alert('Event deleted successfully.');
                    } else {
                        alert('Error: ' + (data.message || 'Could not delete event.'));
                    }
                })
                .catch(() => {
                    alert('Network error. Please try again.');
                });
        }

        // Close modal on backdrop click
        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Keyboard shortcut: Escape closes modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('eventModal').classList.contains('show')) {
                closeModal();
            }
        });
    </script>
@endpush