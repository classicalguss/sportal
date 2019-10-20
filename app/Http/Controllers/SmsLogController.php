<?php

namespace App\Http\Controllers;

use App\Hashes\SmsLogIdHash;
use App\Helpers\SmsHelper;
use App\Http\Requests\SmsLog\SmsLogRequest;
use App\SmsLog;

class SmsLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(SmsLogRequest $request)
    {
        $page_title = __('smslog.title');
        $count = $request->has('count') ? $request->input('count') : env('SMS_LOGS_DEFAULT_PAGINATION', 10);

        $query = SmsLog::query();

        if($request->has('phone_number')){
            $phone_number = $request->input('phone_number');
            $phone_number_like = "%$phone_number%";
            $query->where('phone_number', 'like', $phone_number_like);
        }

        $sms_logs = $query->latest()->paginate($count);
        return view('smslog.index', compact('page_title', 'sms_logs'));
    }

    public function show($id, SmsLogRequest $request)
    {
        $sms_log = $this->checkExistence($id);
        $page_title = __('smslog.title');

        //Check Status
        $sms_log_status = SmsHelper::smsStatus($sms_log->message_id);
        if($sms_log_status['meta']['status_code'] == 200){
            $sms_log->status = $sms_log_status['data']['status'];
            $sms_log->save();
            $sms_log->refresh();
        }

        return view('smslog.show', compact('page_title', 'sms_log'));
    }

    private function checkExistence($id)
    {
        $sms_log = SmsLog::where('id', SmsLogIdHash::private($id))->first();
        if($sms_log == null){
            abort(404);
        }
        return $sms_log;
    }
}
