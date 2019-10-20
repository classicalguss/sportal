@extends('layouts.app')

@section('content')
    @include('common.show-errors')
    @include('common.show-message')

    <div class="container-fluid">
        <form class='form'>
            <div class="row">
                <div class="col-md-6">
                    <input type="checkbox" id="disable-all-form"> <span id='state'>Enabled</span>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" placeholder="Click here to set date" name="daterange" id="reservation">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-4 nopadding"><input type="checkbox" id="disable-interval"> <span id='state'>Interval (Minutes)</span></div>
                    <div class="col-md-4 nopadding"><input type="number" id="interval" min="5" value="5" step="5" class="form-control"></div>
                    <div class="col-md-4 nopadding"><button type="button" id="add-interval" class="btn btn-primary" disabled><i class="glyphicon glyphicon-plus"></i></button></div>
                </div>
            </div>
            <div class="row" id="interval-container">

            </div>
        </form>
        <br>
        <div id='cont'>
            <h4 id='page-header'>Reserve For all Days: <input checked type="checkbox" id="check-all"> <button id="clear-btn" class="btn btn-danger">Clear Checked Days</button></h4>
        </div>
        <div id='cont2'>
            <h4 id='page-header'>Generate Times:</h4>
        </div>
        <br>
        <div id='alert-area'></div>
    </div>
    <div class='container-fluid' id='venu-table'>

        <div class="row">
            <div class="col-md-4">
                <day-table day="SAT"></day-table>
            </div>
            <div class="col-md-4">
                <day-table day="SUN"></day-table>
            </div>
            <div class="col-md-4">
                <day-table day="MON"></day-table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <day-table day="TUS"></day-table>
            </div>
            <div class="col-md-4">
                <day-table day="WED"></day-table>
            </div>
            <div class="col-md-4">
                <day-table day="THU"></day-table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <day-table day="FRI"></day-table>
            </div>
        </div>
    </div>

    <form class="container-fluid form" id="mform" action="{{ route('venues.update.availabilities', $venue->publicId()) }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" id="data-to-send" name="data">
        <button type='submit' class='btn btn-primary pull-right flip' id='submit-data'>@lang('availability.update')</button>
    </form>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-timepicker.min.css') }}">
    <style>
        .nopadding {
            padding: 0;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/availability.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                format: 'YYYY-MM-DD',
                startDate: moment(),
                endDate: moment().add(7, 'days')
            });
        });
        // use this to update the page if you want
        @if($venue_availabilities_times != null)
            window.addEventListener('load', () => updatePage('{!! $venue_availabilities_times !!}'));
        @else

        @endif
    </script>

@endpush