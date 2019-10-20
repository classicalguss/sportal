<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\{
    ForgotPasswordRequest, LoginRequest, PhoneCodeSendRequest, PhoneCodeVerifyRequest, RegisterRequest, ResetPasswordRequest
};
use App\Api\V1\Transformers\{
    BasicTransformer, UserTransformer
};
use App\Helpers\{ImageHelper, PhoneCodeHelper, SmsHelper};
use Tymon\JWTAuth\Exceptions\JWTException;
use App\{
    Hashes\UserIdHash, Image, UserPasswordReset, PhoneNumberVerify, User, SmsLog
};
use Hash;
use Auth;
use Config;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    /**
     * Register new User
     *
     * @param RegisterRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $user = new User($request->all());
        if(!$user->save()) {
            $this->response->errorInternal('User not created');
        }

        if($request->has('image')) {
            $image = $request->file('image');
            $result = ImageHelper::createUserImage($user, $image);
            if($result === false){
                $this->response->errorInternal("Image didn't upload!!");
            }
        }

        //Send Verification Code on success
        $phone_code_send_response = $this->api->with(['phone_number' => $user->phone_number])->post('auth/phone/code/send');
        if($phone_code_send_response['meta']['status_code'] != 200){
            $this->response->accepted([], $this->metaData(null, 202, 'User Created. Error sending verification code send it again'));
        }

        $data = $phone_code_send_response['data']['verify_code'] ? $phone_code_send_response['data'] : null;
        return $this->response->created([], $this->metaData($data, 201, 'User Created'));
    }

    /**
     * Log the user in
     *
     * @param LoginRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;

        $field = 'phone_number';
        if (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        }
        $request->merge([$field => $request->input('username')]);

        $credentials = $request->only([$field, 'password']);

        try {
            $jwt = $this->guard()->attempt($credentials);
            if(!$jwt) {
                $this->response->errorForbidden();
            }
        } catch (JWTException $e) {
            $this->response->errorInternal($e->getMessage());
        }

        $user = $this->guard()->user();
        if($user->status == User::USERSTATUS_BLOCKED){
            $this->response->error('User Blocked', 422);
        }

        $user->jwt = $jwt;
        $user->save();

        $user->extra_data = [
            'jwt' => $jwt,
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
        ];

        return $this->response->item($user, new UserTransformer($return_data))->setMeta($this->metaData());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['meta' => $this->metaData()]);
    }

    /**
     * Forget Password
     *
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(ForgotPasswordRequest $request)
    {
        $phone_number = $request->input('phone_number');

        //Check if already a key generated
        $user_password_reset = UserPasswordReset::where('phone_number', $phone_number)->first();
        if($user_password_reset == null){
            $user_password_reset = new UserPasswordReset;
            $user_password_reset->phone_number = $phone_number;
        }

        //Generate code
        $verify_code = PhoneCodeHelper::generateCode();

        //hash it
        $verify_code_hashed = PhoneCodeHelper::hashCode($verify_code);

        //save it to database table
        $user_password_reset->verify_code=$verify_code_hashed;
        $user_password_reset->save();

        $data = [];
        if(env('SMS_SEND_ENABLE', true)){
            //Send msg
            $message = "Your password reset number is: " . $verify_code;
            $response = SmsHelper::sendSms($phone_number, $message, SmsLog::SMSTYPE_RESET_PASSWORD);
            if($response['meta']['status_code'] != 200){
                return response()->json(['meta' => $this->metaData([], $response['meta']['status_code'], $response['meta']['message'])])->setStatusCode($response['meta']['status_code']);
            }
        } else {
            $data['verify_code'] = $verify_code;
        }

        return response()->json($this->metaData($data));
    }

    /**
     * Reset Password
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $phone_number = $request->input('phone_number');
        $password = $request->input('password');
        $verify_code = $request->input('verify_code');

        $user_password_reset = UserPasswordReset::where('phone_number', $phone_number)->first();
        if($user_password_reset == null || !PhoneCodeHelper::checkCode($verify_code, $user_password_reset->verify_code)){
            $this->response->errorBadRequest('Your Verification code expired, please create new verification code');
        }

        $user = User::where('phone_number', $phone_number)->first();
        $user->password = $password;
        $user->save();

        $user_password_reset->delete();

        return response()->json(['meta' => $this->metaData()]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $user = $this->guard()->user();
        $jwt = $this->guard()->refresh();
        $user->jwt = $jwt;
        $user->save();

        $data = [
            'jwt' => $jwt,
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ];

        return response()->json($this->metaData($data));
    }

    /**
     * Send Verification Code to mobile phone number.
     *
     * @param PhoneCodeSendRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phoneCodeSend(PhoneCodeSendRequest $request)
    {
        $phone_number = $request->input('phone_number');

        //Check if already verified !!
        $user = User::where('phone_number', $phone_number)->first();
        if($user->status == User::USERSTATUS_VERIFIED || $user->status == User::USERSTATUS_BLOCKED){
            $status = 'User already verified';
            if($user->status == User::USERSTATUS_BLOCKED){
                $status = 'User is Blocked!!';
            }

            return response()->json(['meta' => $this->metaData(null, 201, $status)])->setStatusCode(201);
        }

        //Check if already a key generated
        $phone_number_verify = PhoneNumberVerify::where('phone_number', $phone_number)->first();
        if($phone_number_verify == null){
            $phone_number_verify = new PhoneNumberVerify;
            $phone_number_verify->phone_number = $phone_number;
        }

        //Generate code
        $verify_code = PhoneCodeHelper::generateCode();

        //hash it
        $verify_code_hashed = PhoneCodeHelper::hashCode($verify_code);

        //save it to database table
        $phone_number_verify->request_count+=1;
        $phone_number_verify->request_time=time();
        $phone_number_verify->verify_code=$verify_code_hashed;
        $phone_number_verify->save();

        $data = [];
        if(env('SMS_SEND_ENABLE', true)){
            //Send msg
            $message = "Your verification number is: " . $verify_code;
            $response = SmsHelper::sendSms($phone_number, $message, SmsLog::SMSTYPE_VERIFY_PHONE);
            if($response['meta']['status_code'] != 200){
                return response()->json(['meta' => $this->metaData([], $response['meta']['status_code'], $response['meta']['message'])])->setStatusCode($response['meta']['status_code']);
            }
            $data['verify_code'] = 'Send by SMS';
        } else {
            $data['verify_code'] = $verify_code;
        }

        return response()->json($this->metaData($data));
    }

    /**
     * Check if code is correct.
     * @param PhoneCodeVerifyRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function phoneCodeVerify(PhoneCodeVerifyRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;
        $phone_number = $request->input('phone_number');
        $verify_code = $request->input('verify_code');

        $user = User::where('phone_number', $phone_number)->first();
        $phone_number_verify = PhoneNumberVerify::where('phone_number', $phone_number)->first();
        if (!env('SMS_SEND_ENABLE', true) || Hash::check($verify_code, $phone_number_verify->verify_code)) {
            $user->update(['status' => User::USERSTATUS_VERIFIED]);

            try {
                $jwt = JWTAuth::fromUser($user);
                $user->jwt = $jwt;
                $user->save();

                $user->extra_data = [
                    'jwt' => $jwt,
                    'expires_in' => $this->guard()->factory()->getTTL() * 60,
                ];
            } catch (JWTException $e) {
                $this->response->errorInternal($e->getMessage());
            }

            return $this->response->item($user, new UserTransformer($return_data))->setMeta($this->metaData());
        }

        $this->response->errorBadRequest('Verification code not correct');
    }
}
