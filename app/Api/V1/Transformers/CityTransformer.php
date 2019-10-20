<?php

namespace App\Api\V1\Transformers;

use App\City;
use App\Hashes\CityIdHash;

class CityTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param City $city
     * @return array
     */
    public function transform(City $city)
    {
        $data = $this->data($city, $this->getReturnData());
        return $data;
    }

    private function data(City $city, $return_data)
    {
        switch ($return_data) {
            case self::RETURNDATA_FULL:
                $basic = $this->data($city, self::RETURNDATA_BASIC);
                $details = $this->data($city, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                $name = $this->data($city, self::RETURNDATA_NAME);
                $basic = [
                    'cid' => CityIdHash::public($city->id)
                ];
                return array_merge($name, $basic);
            case self::RETURNDATA_DETAILS:
                return [];
            case self::RETURNDATA_NAME:
                return [
                    'name' => [
                        'ar' => $city->name_ar ?? City::DEFAULT_NAME,
                        'en' => $city->name_en ?? City::DEFAULT_NAME,
                    ]
                ];
            case self::RETURNDATA_NONE:
                return [];
        }
        return [];
    }
}