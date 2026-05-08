@extends('mentor.layouts.dashboard')

@section('title')
    my-calender
@endsection

@push('styles')

    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    {{--  jQuery (required for FullCalendar v5)  --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{--  FullCalendar CSS  --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />

    {{--  FullCalendar JS  --}}
    {{--  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>  --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <style>
         .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        #calendar {
            margin-top: 20px;
        }
    </style>

@endpush

@section('content')

    <div class="container">
        <h1>📅 Event Calendar</h1>
        <div id="calendar"></div>
    </div>

@endsection

@push('scripts')

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Parse events from PHP to JavaScript
            var events = @json($events);

            // Format events for FullCalendar
            var formattedEvents = events.map(function(event) {
                return {
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    backgroundColor: event.color || '#3788d8',
                    borderColor: event.color || '#3788d8',
                    extendedProps: {
                        description: event.description
                    }
                };
            });

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: formattedEvents,
                editable: true,
                selectable: true,
                select: function(info) {

                    var TitleMessage = '📅 Event title:\n\n';

                    var title = prompt(TitleMessage, 'New Event');

                    if (title) {
                        var eventData = {
                            title: title,
                            start: info.startStr,
                            end: info.endStr
                        };

                        console.log('Sending data:', eventData);

                        // Add event via AJAX
                        fetch('{{ route('mentor.events.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(eventData)
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('HTTP error ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Success! Data received:', data);
                            calendar.addEvent({
                                id: data.id,
                                title: data.title,
                                start: data.start,
                                end: data.end,
                                backgroundColor: data.color || '#3788d8',
                                borderColor: data.color || '#3788d8'
                            });
                        })
                        .catch(error => {
                            console.error('Error details:', error);
                            alert('Error: ' + error.message);
                        });
                    }
                },

                eventClick: function(info) {
                    alert('Event: ' + info.event.title + '\n' + info.event.start.toLocaleString());
                },
                {{--  eventClick: function(info) {
                    info.el.addEventListener('contextmenu', function(e) {
                        e.preventDefault();

                        var confirmDelete = confirm('Delete event: "' + info.event.title + '"?');

                        if (confirmDelete) {
                            fetch('/mentor/events/' + info.event.id, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    info.event.remove();
                                    alert('Event deleted!');
                                }
                            });
                        }
                    });
                }  --}}
            });

            calendar.render();
        });
    </script>

@endpush


{{--  @include('mentor.partials.construction')  --}}
