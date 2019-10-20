<?php

namespace App\Helpers;

use App\Hashes\UserIdHash;
use App\Hashes\TypeIdHash;
use Storage;
use Intervention\Image\ImageManagerStatic AS Image;

class ImageHelper
{
    /**
     * @param $image
     * @param null|string $filename
     * @return bool
     */
    public static function uploadImage($image, string $filename = null)
    {
        $s3 = Storage::disk('s3');
        $result = $s3->put($filename, file_get_contents($image), 'public');
        if($result !== true){
            return false;
        }
        return true;
    }
    /**
     * @param $binary
     * @param null|string $filename
     * @return bool
     */
    public static function uploadThumb($binary, string $filename = null)
    {
        $s3 = Storage::disk('s3');
        $result = $s3->put($filename, $binary, 'public');
        if($result !== true){
            return false;
        }
        return true;
    }

    /**
     * @param null|string $filename
     * @return bool
     */
    public static function deleteImage(string $filename)
    {
        $s3 = Storage::disk('s3');
        $result = $s3->delete($filename);
        if($result !== true){
            return false;
        }
        return true;
    }

    public static function removeFacilityImage($facility_id, $name)
    {
        $result = false;
        $filename = self::FacilityFilename($facility_id, $name);
        $thumbnail = self::FacilityThumbnail($facility_id, $name);

        $image = \App\Image::where('filename', $filename)->first();
        if($image){
            $result = self::deleteImage($thumbnail);
            $result = self::deleteImage($filename);
            $result = $image->delete();
        }
        return $result;
    }

    public static function createFacilityImage($facility_id, $name, $image, $type, $size)
    {
        $filename = self::facilityFilename($facility_id, $name);
        $thumbnail = self::facilityThumbnail($facility_id, $name);

        $upload_image = self::uploadImage($image, $filename);
        if($upload_image == false){
            abort(400);
        }

        //Upload Thumb
        $thumb = Image::make($image->getRealPath());
        $thumb->resize(200, 200)->save();
        $upload_thumb = self::uploadThumb((string)$thumb, $thumbnail);
        if($upload_thumb == false){
            abort(400);
        }

        //add image to database
        $image = \App\Image::create([
            'filename' => $filename,
            'thumbnail' => $thumbnail,
            'type' => $type,
            'name' => $name,
            'size' => $size,
        ]);

        return $image;
    }

    public static function createTypeImage($type, $image)
    {
        $name = TypeIdHash::public($type->name_en) . '-' . microtime(true) . '.' . $image->getClientOriginalExtension();
        $filename = "types/type_" . $name;
        $upload_image = ImageHelper::uploadImage($image, $filename);
        if($upload_image === false) {
            return false;
        }

        $image = \App\Image::create([
            'filename' => $filename,
            'thumbnail' => $filename,
            'name' => $name,
            'type' => \App\Image::IMAGETYPE_TYPE
        ]);

        $type->image_id = $image->id;
        $type->save();
        return true;
    }

    public static function createUserImage($user, $image)
    {
        //generate unique file name
        $name = UserIdHash::public($user->id) . '-' . microtime(true) . '.' . $image->getClientOriginalExtension();
        $filename = "users/user_" . $name;

        //upload image to S3
        $upload_image = ImageHelper::uploadImage($image, $filename);
        if($upload_image == false){
            return false;
        }

        //add image to database
        $image = \App\Image::create([
            'filename' => $filename,
            'thumbnail' => $filename,
            'name' => $name,
            'type' => \App\Image::IMAGETYPE_USER
        ]);
        $user->image_id = $image->id;
        $user->save();

        return true;
    }

    public static function facilityFilename($facility_id, $name)
    {
        return "facilities/" . $facility_id . '/'. $name;
    }

    public static function FacilityThumbnail($facility_id, $name)
    {
        return "facilities/" . $facility_id . '/thumbnails/'. $name;
    }
}