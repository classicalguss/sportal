@extends('layouts.app')

@section('content')
    @include('common.delete-modal')
    @include('venue.type.create')
    @include('common.show-errors')
    @include('common.show-message')

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title" style='left:10px'>
                        <form class="form-inline">
                            <div class="form-group">
                                <input type="text" name="name" class="form-control input-sm" placeholder="@lang('common.name')" value="{{ request('name') }}">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                            </div>
                        </form>
                    </div>
                    <div class="box-tools">
                        <button class="btn btn-success btn-bg" data-toggle="modal" data-target="#create-type">@lang('type.create')</button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.image')</th>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.color')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($types AS $type)
                            <tr>
                                <td><img class="img-responsive" src="{{ $type->imageFileName() }}" style="width: 30px;height: auto;"></td>
                                <td>{{ $type->name() }}</td>
                                <td>{{ $type->color }}</td>
                                <td>
                                    <form role="form" method="POST" action="{{route('types.destroy', $type->publicId())}}" id="{{$type->publicId()}}" onsubmit="this.preventDefault()" >
                                        <input type="hidden" value="DELETE" name="_method">
                                        {{ csrf_field() }}
                                        <div class="btn-group">
                                            <a href="{{ route('types.edit', $type->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                            <button data-toggle="modal" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$type->publicId()}}')"><i class="fa fa-trash-o"></i></button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody></table>
                </div>
            </div>
        </div>
    </div>
@endsection
