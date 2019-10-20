@extends('layouts.app')

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            @include('reservation.include.show-availability-mini')
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('recursive.reservations')</h3>
        </div>
        <div class="box-body">
            @include('reservation.include.list-reservations')
        </div>
    </div>
@endsection