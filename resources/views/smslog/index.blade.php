@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title" style='left:10px'>
                        <form class="form-inline">
                            <div class="form-group">
                                <input type="text" name="phone_number" class="form-control input-sm" placeholder="@lang('common.phone-number')" value="{{ request('phone_number') }}">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm">@lang('common.filter')</button>
                            </div>
                            <div class="form-group">
                                <a href="{{ route('sms.index') }}" class="btn btn-default btn-sm">@lang('common.reset')</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody><tr>
                            <th>@lang('common.phone-number')</th>
                            <th>@lang('common.message')</th>
                            <th>@lang('common.message-type')</th>
                            <th>@lang('common.status')</th>
                            <th>@lang('common.created-at')</th>
                            <th>@lang('common.actions')</th>
                        </tr>
                        @forelse($sms_logs AS $sms_log)
                        <tr>
                            <td>{{ $sms_log->phone_number }}</td>
                            <td>{{ str_limit($sms_log->message, 50) }}</td>
                            <td><span class="label label-{{ \App\SmsLog::$type_color[$sms_log->message_type] }}">{{ \App\SmsLog::$type[$sms_log->message_type] }}</span></td>
                            <td><span class="label label-{{ \App\SmsLog::$status_color[$sms_log->status] }}">{{ \App\SmsLog::$status[$sms_log->status] }}</span></td>
                            <td>{{ $sms_log->created_at }}</td>
                            <td>
                                <a href="{{ route('sms.show', $sms_log->publicId()) }}" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></a>
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
            {{ $sms_logs->appends(['phone_number' => request('phone_number')])->links() }}
        </div>
    </div>
@endsection
