@extends('layouts.app')

@section('content')
    <section class="invoice">
        <div class="row">
            <div class="col-md-6">
                <h2 class="text-center">{{ \App\SmsLog::$type[$sms_log->message_type] }}</h2>

                <div class="table-responsive margin-bottom">
                    <table class="table">
                        <tbody><tr>
                            <th style="width: 50%" class="text-right flip">@lang('common.phone-number')</th>
                            <td class="text-left flip">{{ $sms_log->phone_number }}</td>
                        </tr>
                        <tr>
                            <th class="text-right flip">@lang('common.message-type')</th>
                            <td class="text-left flip"><span class="label label-{{ \App\SmsLog::$type_color[$sms_log->message_type] }}">{{ \App\SmsLog::$type[$sms_log->message_type] }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-right flip">@lang('common.status')</th>
                            <td class="text-left flip"><span class="label label-{{ \App\SmsLog::$status_color[$sms_log->status] }}">{{ \App\SmsLog::$status[$sms_log->status] }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-right flip">@lang('common.created-at')</th>
                            <td class="text-left flip">{{ $sms_log->created_at }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row margin-bottom">
                    <h3 class="text-center">@lang('common.message')</h3>
                    <h4>{{ $sms_log->message }}</h4>
                </div>
            </div>
        </div>
    </section>
@endsection