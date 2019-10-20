@extends('layouts.app')

@section('content')
    @include('common.delete-modal')
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
                                <input type="text" name="email" class="form-control input-sm" placeholder="@lang('common.email')" value="{{ request('email') }}">
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone_number" class="form-control input-sm" placeholder="@lang('common.phone-number')" value="{{ request('phone_number') }}">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                            </div>
                            <div class="form-group">
                                <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">@lang('common.reset')</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.name')</th>
                            <th>@lang('common.email')</th>
                            <th>@lang('common.phone-number')</th>
                            <th>@lang('common.birthday')</th>
                            <th>@lang('common.status')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($users AS $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->birth_date }}</td>
                            <td><span class="label label-{{ $user->userStatusColor() }}">{{ $user->userStatus() }}</span></td>
                            <td>
                                <form role="form" method="POST" action="{{route('users.destroy', $user->publicId())}}" id="{{$user->publicId()}}" onsubmit="this.preventDefault()">
                                    <input type="hidden" value="DELETE" name="_method">
                                    {{ csrf_field() }}
                                    <div class="btn-group">
                                        <a href="{{ route('users.show', $user->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
                                        <a href="{{ route('users.edit', $user->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                        <button data-toggle="modal" data-target="#deleteModal" type="button" class="btn btn-danger btn-sm" onclick="setDeleteFormId('{{$user->publicId()}}')"><i class="fa fa-trash-o"></i></button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6">@lang('common.no-results')</td>
                            </tr>
                        @endforelse
                        </tbody></table>
                </div>
            </div>
            {{ $users->appends(['name' => request('name'), 'email' => request('email')])->links() }}
        </div>
    </div>
@endsection
