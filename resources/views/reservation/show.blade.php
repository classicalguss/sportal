@extends('layouts.app')

@section('content')
    @include('common.show-message')
    @include('reservation.include.show-reservation')
    @include('reservation.include.show-availability')
@endsection