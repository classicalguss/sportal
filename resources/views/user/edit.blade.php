@extends('layouts.app')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('user.update')</h3>
        </div>
        <form role="form" method="POST" action="{{route('users.update', $user->publicId())}}">
            <input type="hidden" value="PATCH" name="_method">
            {{ csrf_field() }}
            <div class="box-body">
                @include('common.show-message')
                @include('user.include.user-info')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">@lang('common.status')</label>
                            <select class="form-control" id="status" name="status">
                                @php
                                    $status = $user->status ?? old('status') ?? 9;
                                @endphp
                                <option value='9' {{ $status == 9 ? 'selected' : '' }}>@lang('user.status-unknown')</option>
                                <option value='0' {{ $status == 0 ? 'selected' : '' }}>@lang('user.status-new')</option>
                                <option value='1' {{ $status == 1 ? 'selected' : '' }}>@lang('user.status-verified')</option>
                                <option value='2' {{ $status == 2 ? 'selected' : '' }}>@lang('user.status-blocked')</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">@lang('user.update')</button>
            </div>
        </form>
    </div>
@endsection