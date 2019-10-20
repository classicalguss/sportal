@extends('layouts.app')

@section('content')
    @include('common.delete-modal')
    @include('common.show-message')
    @include('venue.include.index')
@endsection