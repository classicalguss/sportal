<?php

namespace App\Api\V1\Transformers;

use App\City;
use App\Hashes\RegionIdHash;
use App\Region;

class RegionTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Region $region
     * @return array
     * @internal param Region $region
     */
    public function transform(Region $region)
    {
        $region_data = $this->data($region, $this->getReturnData());

        if($region->extra_data){
            foreach($region->extra_data AS $key => $value){
                $data[$key] = $value;
            }
            $data['region'] = $region_data;
        } else {
            $data = $region_data;
        }

        return $data;
    }

    private function data(Region $region, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($region, self::RETURNDATA_BASIC);
                $details = $this->data($region, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                $name = $this->data($region, self::RETURNDATA_NAME);
                $basic = [
                    'rid' => RegionIdHash::public($region->id)
                ];
                return array_merge($name, $basic);
            case self::RETURNDATA_DETAILS:
                $city_transformer = new CityTransformer(self::RETURNDATA_BASIC);
                return [
                    'city' => $city_transformer->transform($region->city() ?? new City()),
                ];
            case self::RETURNDATA_NAME:
                return [
                    'name' => [
                        'ar' => $region->name_ar ??  Region::DEFAULT_NAME,
                        'en' => $region->name_en ?? Region::DEFAULT_NAME,
                    ]
                ];
            case self::RETURNDATA_NONE:
                return [];
        }
        return [];
    }
}