<?php

namespace App\Helpers;

use App\SmsLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SmsHelper
{
    private static function responseError($error_message = 'Missing Required Data', $status_code = 400)
    {
        $meta = ["status_code" => $status_code, "error_message" => $error_message];
        return ["meta" => $meta];
    }

    private static function responseSuccess($meta, $data = null)
    {
        $output = ["meta" => $meta];
        if($data != null){
            $output['data'] = $data;
        }
        return $output;
    }

    private static function getMessageId($text)
    {
        preg_match('#\((.*?)\)#', $text, $match); //'I01-SMS (SMS-ID) queued for processing.'
        $message_id = $match[1] ?? 0;
        return $message_id;
    }

    /**
     * @param int $phone_number
     * @param string $message
     * @param int $message_type
     * @return array
     */
    public static function sendSms(int $phone_number, string $message, int $message_type = SmsLog::SMSTYPE_DEFAULT)
    {
        $base_url = env('SMS_BASE_URL', 'https://smsgate.arabiacell.net/index.php/api/');
        $client = new Client();
        $res = null;
        try {
            $res = $client->request('POST', $base_url . 'send_sms/send', [
                'form_params' => [
                    'mobile_number' => $phone_number, //9627[7/8/9]1234567
                    'msg' => $message, //'Your verification code is: 123456'
                    'from' => env('SMS_SENDER_ID', 'Sportal-App'),
                    'tag' => 1
                ],
                'auth' => [
                    env('SMS_BASICAUTH_USER', 'Sportal_API'),
                    env('SMS_BASICAUTH_PASS', 'NcusKkf~YqB}3P')
                ]
            ]);
        } catch (RequestException $e) {
            return self::responseError('SMS API error: ' . $e->getMessage(), 500);
        }

        $body = json_decode($res->getBody(), true);
        $message_id = self::getMessageId($body['message']);

        //add to sms logs
        $sms_log = SmsLog::create([
            'phone_number' => $phone_number,
            'message' => $message,
            'message_id' => $message_id,
            'message_type' => $message_type,
            'status' => SmsLog::SMSSTATUS_SEND
        ]);

        return self::responseSuccess(['status_code' => 200], $body);
    }

    /**
     * @return array
     */
    public static function accountDetails()
    {
        $base_url = env('SMS_BASE_URL', 'https://smsgate.arabiacell.net/index.php/api/');
        $client = new Client();
        $res = null;
        try {
            $res = $client->request('GET', $base_url . 'get_info/get', [
                'query' => [
                    'type' => 1
                ],
                'auth' => [
                    env('SMS_BASICAUTH_USER', 'Sportal_API'),
                    env('SMS_BASICAUTH_PASS', 'NcusKkf~YqB}3P')
                ]
            ]);
        } catch (RequestException $e) {
            return self::responseError('SMS API error: ' . $e->getMessage(), 500);
        }

        $body = json_decode($res->getBody(), true);
        return self::responseSuccess(['status_code' => 200], $body);
    }

    /**
     * @param $sms_id
     * @return array
     */
    public static function smsStatus($sms_id)
    {
        $base_url = env('SMS_BASE_URL', 'https://smsgate.arabiacell.net/index.php/api/');
        $client = new Client();
        $res = null;
        try {
            $res = $client->request('GET', $base_url . 'status/get', [
                'query' => [
                    'smsId' => $sms_id
                ],
                'auth' => [
                    env('SMS_BASICAUTH_USER', 'Sportal_API'),
                    env('SMS_BASICAUTH_PASS', 'NcusKkf~YqB}3P')
                ]
            ]);
        } catch (RequestException $e) {
            return self::responseError('SMS API error: ' . $e->getMessage(), 500);
        }

        $body = json_decode($res->getBody(), true);
        $status = isset($body[$sms_id]) ? $body[$sms_id]['dlr_status'] : SmsLog::SMSSTATUS_SEND;
        return self::responseSuccess(['status_code' => 200], ['status' => $status]);
    }
}