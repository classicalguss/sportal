@extends('layouts.app')

@section('content')
    @include('common.show-message')

    @include('reservation.include.filter-list')

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div id='calendar' style="max-width: 1272px;margin: 0 auto;"></div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.print.css') }} " media="print">
@endpush

@push('scripts')
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                defaultDate: moment().format('YYYY-MM-DD'),
                defaultView: 'agendaWeek',
                editable: false,
                timeFormat: 'HH:mm',
                scrollTime: '12:00:00',
                eventLimit: true, // allow "more" link when too many events
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'today',
                    month: 'month',
                    week: 'week',
                    day: 'day'
                },
                events: [
                    @foreach($availabilities AS $availability)
                    {
                        @if($availability->status == \App\VenueAvailability::AVAILABILITYSTATUS_AVAILABLE)
                            title: '@lang('availability.status-available')',
                            backgroundColor: '#00a65a',
                            borderColor: '#00a65a',
                            url: '{{ route('reservations.create', $availability->publicId()) }}',
                        @else
                            title: '@lang('availability.status-reserved')',
                            backgroundColor: '#dd4b39',
                            borderColor: '#dd4b39',
                        @endif
                        start: '{{ $availability->date }}T{{ $availability->time_start }}',
                        end: '{{ $availability->date }}T{{ $availability->time_finish }}',
                    },
                    @endforeach
                ]
            });
        });
    </script>
@endpush