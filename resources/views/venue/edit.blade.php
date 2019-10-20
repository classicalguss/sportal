@extends('layouts.app')

@section('content')
    <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('venue.update')</h3>
        </div><!-- /.box-header -->
        <!-- form start -->
        <form role="form" method="POST" action="{{route('venues.update', $venue->publicId())}}">
            <input type="hidden" value="PATCH" name="_method">
            {{ csrf_field() }}
            @include('venue.form')
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@lang('venue.update')</button>
            </div>
        </form>
    </div>

    <div class="row">
        @if($venue->kind != \App\Venue::VENUEKIND_MULTIPLE)
        <div class="col-md-4">
            @include('common.create-marker')
        </div>
        @endif
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
                            <a href="{{ route('venues.images.edit', $venue->publicId()) }}" class="btn btn-primary">@lang('common.update-images')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($venue->kind != \App\Venue::VENUEKIND_MULTIPLE)
        @can(\App\Permission::PERMISSION_UPDATE_VENUE_AVAILABILITIES)
        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('venue.update-availabilities')</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row margin-bottom">
                        <div class="col-md-12">
                            <div class="table-responsive margin-bottom">
                                <table class="table">
                                    <tbody><tr>
                                        <th style="width: 50%" class="text-right flip">@lang('venue.auto-generate')</th>
                                        <td class="text-left flip">
                                            @if($venue->availabilities_auto_generate == true)
                                                <span class="label label-success">Enable</span>
                                            @else
                                                <span class="label label-danger">Disable</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right flip">@lang('venue.date-start')</th>
                                        <td class="text-left flip">{{ $venue->availabilities_date_start }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right flip">@lang('venue.date-finish')</th>
                                        <td class="text-left flip">{{ $venue->availabilities_date_finish }}</td>
                                    </tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-4 text-center">
                            <a href="{{ route('venues.edit.availabilities', $venue->publicId()) }}" class="btn btn-primary">@lang('venue.update-availabilities')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @endif
    </div>
@endsection
