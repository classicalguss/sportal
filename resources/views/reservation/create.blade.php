@extends('layouts.app')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('reservation.create')</h3>
        </div>
        <form role="form" method="POST" action="{{route('reservations.store')}}">
            {{ csrf_field() }}
            <input type="hidden" name="vaids" value="{{ $ids }}">
            <input type="hidden" name="vid" value="{{ $vid }}">
            <input type="hidden" name="time_start" value="{{ $venue_availability->time_start }}">
            <input type="hidden" name="time_finish" value="{{ $venue_availability->time_finish }}">
            <input type="hidden" name="duration" value="{{ $venue_availability->duration }}">
            <div class="box-body">
                @include('common.show-errors')
                @include('reservation.form')
                @include('reservation.include.show-availability')
            </div>
        </form>
    </div>
@endsection