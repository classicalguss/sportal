<?php

namespace App\Api\V1\Transformers;

use App\Hashes\MarkerIdHash;
use App\Marker;
use Config;

class MarkerTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Marker $marker
     * @return array
     */
    public function transform(Marker $marker)
    {
        $data = $this->data($marker, $this->getReturnData());
        return $data;
    }

    private function data(Marker $marker, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($marker, self::RETURNDATA_BASIC);
                $details = $this->data($marker, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                return [
                    'lat' => $marker->latitude ?? Marker::DEFAULT_LATITUDE,
                    'lng' => $marker->longitude ?? Marker::DEFAULT_LONGITUDE,
                ];
            case self::RETURNDATA_DETAILS:
                return [
                    'mid' => MarkerIdHash::public($marker->id),
                    'name' => [
                        'ar' => $marker->name_ar ?? Marker::DEFAULT_NAME,
                        'en' => $marker->name_en ?? Marker::DEFAULT_NAME,
                    ]
                ];
            case self::RETURNDATA_NONE:
                return [];
        }

        return [];
    }
}