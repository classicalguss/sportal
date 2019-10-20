@extends('layouts.app')

@section('content')
    @include('user.include.user-info')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('user.reservations')</h3>
                </div>
                <div class="box-body">
                    @include('reservation.include.list-reservations')
                </div>
                {{ $reservations->appends(['name' => request('name'), 'email' => request('email')])->links() }}
            </div>
        </div>
    </div>
@endsection