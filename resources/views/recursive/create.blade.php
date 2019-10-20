@extends('layouts.app')

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            @include('reservation.include.show-availability-mini')
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            @include('recursive.include.recursive-dates')
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            @include('recursive.include.recursive-list')
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('reservation.create')</h3>
        </div>
        <div class="box-body">
            @include('common.show-errors')
            <form role="form" method="POST" action="{{route('recursive.store')}}">
                {{ csrf_field() }}
                <input type="hidden" name="vaids" value="{{ $ids }}">
                <input type="hidden" name="time_start" value="{{ $venue_availability->time_start() }}">
                <input type="hidden" name="time_finish" value="{{ $venue_availability->time_finish() }}">
                <input type="hidden" name="duration" value="{{ $venue_availability->duration() }}">
                <input type="hidden" name="venue_id" value="{{ $venue_availability->venue()->publicId() }}">
                <input type="hidden" name="date_range" value="{{ implode(',', $dates)}}">
                <input type="hidden" name="dates" value="{{ json_encode($reservations_availability) }}">
                <input type="hidden" name="days" value="{{ json_encode($days) }}">

                @include('reservation.form')
            </form>
        </div>
    </div>

@endsection