<?php

namespace App\Api\V1\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Auth;

class BaseController extends Controller
{
    use Helpers;

    /**
     * Basic Meta Data
     *
     * @param null $data
     * @param int $status
     * @param string $message
     * @return array
     */
    protected function metaData($data = null, $status = 200, $message = "Success")
    {
        $meta = [
            'message' => $message,
            'status_code' => $status
        ];

        if($data != null){
            $response['meta'] = $meta;
            $response['data'] = $data;
        } else {
            $response = $meta;
        }

        return  $response;
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('api');
    }
}