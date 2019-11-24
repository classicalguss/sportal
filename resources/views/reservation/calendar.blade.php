<?php

use App\Reservation;
use Carbon\CarbonInterval;
/** @var $reservations Reservation */
?>
@extends('layouts.app')

@section('content')
    @include('common.show-message')

    <div class="row">
        <div class="col-sm-2">
            <select style="margin-bottom: 10px" onchange="changeFacility(this.value)">
                @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}" {{ $facility->id == $facility_id ? 'selected' : '' }}>{{ $facility->name() }}</option>
                @endforeach
            </select>
            @foreach ($venues as $venue)
                <div class="grabbable grab img-rounded calendar-draggable"
                     id='draggable-facility{{ $venue->id }}'
                     style="margin-bottom: 10px; background: {{ $colorsKeyArray[$venue->id] }}"
                     data-event='{
                        "color": "{{ $colorsKeyArray[$venue->id] }}",
                        "title": "{{ $venue->name() }}",
                        "duration": "{{ CarbonInterval::minutes($venue->shortestDuration())->cascade()->format('%H:%I') }}",
                        "venue": "{{ $venue->id }}",
                        "vid": "{{ $venue->publicId() }}",
                        "type": "{{ $venue->types[0]->publicId() }}",
                        "venue_price" : {{ $venue->price }},
                        "stick": true
                     }'
                >
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
        function changeFacility(id) {
            window.location.href = "{{ route('reservations.calendar') }}?facility_id="+id;
        }

        function reservationUpdate(event, revertFunc) {
            duration =moment.duration(event.end - event.start);
            data = {
                "time_start" : event.start.format("DD-MM-YYYY HH:mm"),
                "time_finish": event.end.format("DD-MM-YYYY HH:mm"),
                "reservation_id" : event.reservation_id,
                "vid": event.vid,
                "duration": duration.hours()+":"+duration.minutes(),
                "type": event.type,
                "reservation_type": 1,
            }
            $.ajax('/reservationsCalendarUpdate',{
                'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                'type': 'post',
                "data": data
            })
            .fail(function(response) {
                revertFunc();
                alert(response.responseJSON.message)
            })
            .success(function(response) {
            })
        }

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
                        title: "{{ $reservation->venue()->name() }}{{$reservation->customer()->firstName()." (".$reservation->customer()->phone_number.")"}}",
                        backgroundColor: "{{ $colorsKeyArray[$reservation->venue_id] }}",
                        borderColor: "{{ $colorsKeyArray[$reservation->venue_id] }}",
                        start: '{{ $reservation->start_date_time }}',
                        end: '{{ $reservation->finish_date_time }}',
                        venue: "{{ $reservation->venue_id }}",
                        vid: "{{ $reservation->venue()->publicId() }}",
                        type: "{{ $reservation->venue()->types[0]->publicId() }}",
                        reservation_id: "{{ $reservation->id }}"
                    },
                    @endforeach
                ],
                eventDrop: function(event, delta, revertFunc) {

                    alert('dropped');
                },
                eventResize: function(event, delta, revertFunc) {
                    reservationUpdate(event, revertFunc);
                },
                eventReceive: function(event,delta) {

                    duration =moment.duration(event.end - event.start);
                    durationFactor = duration.asMinutes() / 60.0;
                    price = event.venue_price * durationFactor;
                    data = {
                        "time_start" : event.start.format("DD-MM-YYYY HH:mm"),
                        "time_finish": event.end.format("DD-MM-YYYY HH:mm"),
                        "vid": event.vid,
                        "type": event.type,
                        "duration": duration.hours()+":"+duration.minutes(),
                        "reservation_type": 1,
                        "price": price
                    }
                    $.ajax('/reservationsCalendarStore',{
                        'headers': {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        'type': 'post',
                        "data": data
                    })
                    .fail(function(response) {
                        alert(response.responseJSON.message)
                        $('#calendar').fullCalendar('removeEvents', event._id);
                    })
                    .success(function(response) {
                        event.reservation_id = response.reservation_id;
                    })
                },
                drop() {
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
