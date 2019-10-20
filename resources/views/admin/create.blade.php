@extends('layouts.app')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('admin.create')</h3>
        </div>
        <form role="form" method="POST" action="{{route('admins.store')}}">
            {{ csrf_field() }}
            @include('admin.form')
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@lang('admin.create')</button>
            </div>
        </form>
    </div>
@endsection