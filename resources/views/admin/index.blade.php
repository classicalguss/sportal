@extends('layouts.app')

@section('content')
    @include('common.delete-modal')
    @include('common.show-message')

    <!-- Your Page Content Here -->
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
                                <input type="text" name="email" class="form-control input-sm" placeholder="@lang('common.email')" value="{{ request('email') }}">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                            </div>
                            <div class="form-group">
                                <a href="{{ route('admins.index') }}" class="btn btn-default btn-sm">@lang('common.reset')</a>
                            </div>
                        </form>
                    </div>
                    <div class="box-tools">
                        <a href="{{ route('admins.create') }}" class="btn btn-success btn-bg">@lang('admin.create')</a>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.email')</th>
                            <th>@lang('common.role')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($admins AS $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                @foreach($admin->getRoleNames() AS $role)
                                    @if($role == 'super_admin')
                                        <span class="label label-success">@lang('role.super_admin')</span>
                                    @elseif($role == 'facility_manager')
                                        <span class="label label-primary">@lang('role.facility_manager')</span>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <form role="form" method="POST" action="{{route('admins.destroy', $admin->publicId())}}" id="{{$admin->publicId()}}" onsubmit="this.preventDefault()">
                                    <input type="hidden" value="DELETE" name="_method">
                                    {{ csrf_field() }}
                                    <div class="btn-group">
                                        <a href="{{ route('admins.edit', $admin->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                        <button data-toggle="modal" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$admin->publicId()}}')"><i class="fa fa-trash-o"></i></button>
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
            {{ $admins->appends(['name' => request('name'), 'email' => request('email')])->links() }}
        </div>
    </div>
@endsection
