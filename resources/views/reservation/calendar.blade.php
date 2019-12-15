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
<div class="modal fade" id="update-reservation-fields">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="POST" action="">
                <input type="hidden" id="update-reservation-id" name="reservation_id">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="box-title">@lang('reservation.reservation-details')</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_number">@lang('common.phone-number')</label>
                                <div class="input-group date">
                                    @if(app()->isLocale('en'))<div class="input-group-addon">962</div>@endif
                                    <input style="direction: ltr" type="text" class="form-control" id="update-reservation-phone" name="phone_number" value="" placeholder="ex: 791234567">
                                    @if(app()->isLocale('ar'))<div class="input-group-addon">962</div>@endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('common.name')</label>
                                <input type="text" class="form-control" id="update-reservation-name" name="name" value="" placeholder="@lang('common.name')">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">@lang('common.type') *</label>
                                <select class="form-control" id="update-reservation-select" name="type" >
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="price">@lang('common.price')</label>
                            <div class="input-group">
                                <input type="text" id="update-reservation-price" name="price" value="" class="form-control" placeholder="@lang('common.price')">
                                <span class="input-group-addon"><strong>@lang('common.jordanian-dinar')</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left flip" data-dismiss="modal">@lang('common.cancel')</button>
                    <button id="submit" type="submit" class="btn btn-primary">@lang('common.submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
        venueVenueTypes = [];
        @foreach ($venues as $venue)
            venueVenueTypes["{{$venue->id}}"] = JSON.parse("{{ $venue->types->pluck('id') }}")
        @endforeach
        types = [];
        @foreach ($types as $type) {
            types["{{ $type->id }}"] = "{{ \Illuminate\Support\Facades\App::getLocale() == "ar" ? $type->name_ar : $type->name_en }}"
        }
        @endforeach
        function changeFacility(id) {
            window.location.href = "{{ route('reservations.calendar') }}?facility_id="+id;
        }
        function eventLeftCalendar(jsEvent) {
            var calendarPosition = $('#calendar').offset();
            var xPosition = jsEvent.clientX;
            var yPosition = jsEvent.clientY;
            if (xPosition + 20 < calendarPosition.left || yPosition + 20 < calendarPosition.top) {
                return true;
            }
            return false;
        }

        function reservationUpdate(event, revertFunc) {
            duration =moment.duration(event.end - event.start);
            data = {
                "time_start" : event.start.format("DD-MM-YYYY HH:mm"),
                "time_finish": event.end.format("DD-MM-YYYY HH:mm"),
                "reservation_id" : event.reservation_id,
                "duration": duration.hours()+":"+duration.minutes(),
                "vid": event.vid,
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

        $('#update-reservation-fields').submit(function(e) {
            e.preventDefault();
            // Coding
            submittedData = $('form').serializeArray();
            var submittedArray = {};
            for (var i = 0; i < submittedData.length; i++){
                submittedArray[submittedData[i]['name']] = submittedData[i]['value'];
            }
            $.ajax("{{route('reservations.calendarDetailsUpdate')}}", {
                'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                'type': 'post',
                data: $('form').serialize()
            })
            .fail(function (response) {
                alert(response.responseJSON.message)
            })
            .success(function(response) {
                eventUpdated.price = submittedArray.price;
                eventUpdated.type_id = parseInt(submittedArray.type);
                eventUpdated.phonenumber = "962"+submittedArray.phone_number;
                eventUpdated.name = submittedArray.name;
                $('#update-reservation-fields').modal('toggle'); //or  $('#IDModal').modal('hide');
            });

            return false;
        });
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
                columnFormat: 'ddd DD/MM',
                slotDuration: {'minutes' : 30},
                slotLabelInterval: "01:30",
                slowMinutes: 30,
                plugins: [ 'dayGrid' ],
                editable: true,
                timeFormat: 'HH:mm',
                eventLimit: true, // allow "more" link when too many events
                dragRevertDuration: 100,
                minTime: '08:00:00',
                timeFormat: 'h:mm A',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek,agendaDay'
                },
                slotLabelFormat: [
                    'ddd'        // lower level of text
                ],
                buttonText: {
                    today: 'today',
                    week: 'week',
                    day: 'day'
                },
                firstDay: moment().weekday(),
                events: [
                    @foreach($reservations AS $reservation)
                    {
                        title: "{{$reservation->customer->firstName()." (".$reservation->customer->phone_number.")"}} {{ $reservation->venue->name() }}",
                        backgroundColor: "{{ $colorsKeyArray[$reservation->venue_id] }}",
                        borderColor: "{{ $colorsKeyArray[$reservation->venue_id] }}",
                        start: '{{ $reservation->start_date_time }}',
                        end: '{{ $reservation->finish_date_time }}',
                        venue: "{{ $reservation->venue_id }}",
                        vid: "{{ $reservation->venue->publicId() }}",
                        type: "{{ $reservation->venue->types[0]->publicId() }}",
                        type_id: "{{ $reservation->type_id }}",
                        reservation_id: "{{ $reservation->id }}",
                        phonenumber: "{{ $reservation->customer->phone_number }}",
                        name: "{{ $reservation->customer->name }}",
                        price: "{{ $reservation->price }}"
                    },
                    @endforeach
                ],
                eventDragStop: function(event, jsEvent, ui, view) {
                    if (eventLeftCalendar(jsEvent)) {
                        data = {
                            "reservation_id" : event.reservation_id,
                        }
                        $.ajax('/reservationsCalendarDelete',{
                            'headers': {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            'type': 'post',
                            "data": data
                        })
                        $('#calendar').fullCalendar('removeEvents', event._id);
                    }
                },
                eventDrop: function(event, delta, revertFunc) {
                    reservationUpdate(event, revertFunc);
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
                        "price": price,
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
                eventOverlap: function(stillEvent, movingEvent) {
                    return stillEvent.venue != movingEvent.venue
                },
                eventClick: function(event) {
                    eventUpdated = event;
                    $('#update-reservation-fields').modal();
                    $('#update-reservation-id').val(event.reservation_id);
                    $('#update-reservation-name').val(event.name);
                    $('#update-reservation-phone').val(event.phonenumber.substr(3));
                    $('#update-reservation-price').val(event.price);

                    /* Remove all options from the select list */
                    var updateReservationSelect = $('#update-reservation-select').get(0);

                    while (updateReservationSelect.options.length > 0) {
                        updateReservationSelect.remove(updateReservationSelect.options.length - 1);
                    }
                    var venueTypes = venueVenueTypes[event.venue];

                    for (i = 0; i < venueTypes.length; i++)
                    {
                        var opt = document.createElement('option');

                        opt.value = venueTypes[i];
                        opt.text = types[venueTypes[i]];
                        if (event.type_id == opt.value) {
                            opt.selected = "selected";
                        }

                        updateReservationSelect.add(opt, null);
                    }
                },
                allDaySlot: false,
                droppable: true,
            });
        });
    </script>
@endpush
