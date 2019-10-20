@extends('layouts.app')

@section('content')
    <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('admin.update')</h3>
        </div><!-- /.box-header -->
        <!-- form start -->
        <form role="form" method="POST" action="{{route('admins.update', $admin->publicId())}}">
            <input type="hidden" value="PATCH" name="_method">
            {{ csrf_field() }}
            @include('admin.form')
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@lang('admin.update')</button>
            </div>
        </form>
    </div>
@endsection