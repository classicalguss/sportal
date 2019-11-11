<?php

use App\Reservation;
/** @var $reservations Reservation */
?>
@extends('layouts.app')

@section('content')
    @include('common.show-message')

    <div class="row">
    <div class="col-sm-2">
        @foreach ($venues as $venue)
            <div class="grabbable grab img-rounded calendar-draggable"
                 id='draggable-facility{{ $venue->id }}'
                 style="margin-bottom: 10px; background: {{ $colorsKeyArray[$venue->id] }}"
                 data-event='{ "color": "{{ $colorsKeyArray[$venue->id] }}", "title": "my event", "duration": "02:00", "venue": "{{ $venue->id }}" }'>
                <span style="color: white; font-weight: bold">
                    {{ " ".$venue->name() }}
                </span>
            </div>
        @endforeach
    </div>


        <div class="col-sm-10">
            <div id='calendar'></div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.print.css') }} " media="print">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush

@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            @foreach ($venues as $venue)
                $('#draggable-facility{{ $venue->id }}').draggable({
                    zIndex: 999,
                    revertDuration: 0,
                    revert: true,
                });
            @endforeach

            $('#calendar').fullCalendar({
                defaultDate: moment().format('YYYY-MM-DD'),
                defaultView: 'agendaWeek',
                plugins: [ 'dayGrid' ],
                editable: true,
                timeFormat: 'HH:mm',
                eventLimit: true, // allow "more" link when too many events
                minTime: '08:00:00',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'today',
                    week: 'week',
                    day: 'day'
                },
                firstDay: moment().weekday(),
                events: [
                    @foreach($reservations AS $reservation)
                    {
                        title: "{{$reservation->customer()->firstName()." (".$reservation->customer()->phone_number.")"}}",
                        backgroundColor: "{{ $colorsKeyArray[$reservation->venue_id] }}",
                        borderColor: "{{ $colorsKeyArray[$reservation->venue_id] }}",
                        start: '{{ $reservation->start_date_time }}',
                        end: '{{ $reservation->finish_date_time }}',
                        venue: "{{ $reservation->venue_id }}"
                    },
                    @endforeach
                ],
                eventDrop: function(event, delta, revertFunc) {

                    alert("An event was dropped");
                },
                eventReceive: function(info) {

                },
                eventOverlap: function(stillEvent, movingEvent) {
                    return stillEvent.venue != movingEvent.venue
                },
                allDaySlot: false,
                droppable: true,
            });
        });
    </script>
@endpush
