<?php

namespace App\Api\V1\Transformers;

use App\Hashes\UserIdHash;
use App\User;
use Carbon\Carbon;

class UserTransformer extends BasicTransformer
{
    const DEFAULT_IMAGE_FILENAME = "default/user.png";

    /**
     * Turn this item object into a generic array
     *
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        $user_data = $this->data($user, $this->getReturnData());

        if($user->extra_data){
            foreach($user->extra_data AS $key => $value){
                $data[$key] = $value;
            }
            $data['user'] = $user_data;
        } else {
            $data = $user_data;
        }

        return $data;
    }

    private function data(User $user, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($user, self::RETURNDATA_BASIC);
                $details = $this->data($user, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                return [
                    'uid' => UserIdHash::public($user->id),
                    'name' => $user->name ?? User::DEFAULT_NAME,
                    'phone_number' => $user->phone_number ?? User::DEFAULT_PHONE_NUMBER,
                    'email' => $user->email ?? User::DEFAULT_EMAIL,
                    'status' => User::$status[$user->status] ?? User::DEFAULT_STATUS
                ];
            case self::RETURNDATA_DETAILS:
                return [
                    'birth_date' => $user->birth_date ?? USER::DEFAULT_BIRTHDAY,
                    'image' => self::userImage($user)
                ];
            case self::RETURNDATA_NONE:
                return [];
        }
        return [];
    }

    public static function userImage(User $user)
    {
        $image = $user->image()->first();
        $filename = $image == null ? self::DEFAULT_IMAGE_FILENAME : $image->filename;
        $image_path = ImageTransformer::getImagePath($filename);
        return $image_path;
    }
}