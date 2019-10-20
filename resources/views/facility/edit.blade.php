@extends('layouts.app')

@section('content')
    <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('facility.update')</h3>
        </div><!-- /.box-header -->
        <!-- form start -->
        <form role="form" method="POST" action="{{route('facilities.update', $facility->publicId())}}">
            <input type="hidden" value="PATCH" name="_method">
            {{ csrf_field() }}
            @include('facility.form')
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@lang('facility.update')</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-4">
            @include('common.create-marker')
        </div>
        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('common.update-images')</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row margin-bottom">
                        <div class="col-md-12">
                            @include('common.images-slider')
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-4 text-center">
                            <a href="{{ route('facilities.images.edit', $facility->publicId()) }}" class="btn btn-primary">@lang('common.update-images')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
