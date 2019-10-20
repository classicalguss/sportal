@extends('layouts.app')

@section('content')
    @include('common.delete-modal')
    @include('facility.create')
    @include('common.show-errors')
    @include('common.show-message')

    <!-- Your Page Content Here -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title" style='left:10px'>
                        <form class="form-inline">
                            <div class="form-group">
                                <div class='input-group'>
                                    <input type="text" name="name" class="form-control input-sm" placeholder="@lang('common.name')" value="{{ request('name') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                            </div>
                        </form>
                    </div>
                    <div class="box-tools">
                        <button class="btn btn-success btn-bg" data-toggle="modal" data-target="#create-facility">@lang('facility.create')</button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.city')</th>
                            <th>@lang('common.marker')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($facilities AS $facility)
                        <tr>
                            <td>{{ $facility->name() }}</td>
                            <td>{{ $facility->cityName() }}</td>
                            <td>{{ $facility->markerName() }}</td>
                            <td>
                                <form role="form" method="POST" action="{{route('facilities.destroy', $facility->publicId())}}" id="{{$facility->publicId()}}" onsubmit="this.preventDefault()" >
                                    <input type="hidden" value="DELETE" name="_method">
                                    {{ csrf_field() }}
                                    <div class="btn-group">
                                        <a href="{{ route('facilities.show', $facility->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                                        <a href="{{ route('facilities.edit', $facility->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                        <button data-toggle="modal" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$facility->publicId()}}')"><i class="fa fa-trash-o"></i></button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody></table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            {{ $facilities->appends(['name' => request('name')])->links() }}
        </div>
    </div>
@endsection
