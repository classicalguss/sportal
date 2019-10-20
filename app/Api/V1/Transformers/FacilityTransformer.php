<?php

namespace App\Api\V1\Transformers;

use App\City;
use App\Facility;
use App\Hashes\FacilityIdHash;
use App\Image;
use App\Marker;
use App\Region;

class FacilityTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Facility $facility
     * @return array
     */
    public function transform(Facility $facility)
    {
        $facility_data = $this->data($facility, $this->getReturnData());

        if($facility->extra_data){
            foreach($facility->extra_data AS $key => $value){
                $data[$key] = $value;
            }
            $data['facility'] = $facility_data;
        } else {
            $data = $facility_data;
        }

        return $data;
    }

    private function data(Facility $facility, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($facility, self::RETURNDATA_BASIC);
                $details = $this->data($facility, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                $city_transformer = new CityTransformer(self::RETURNDATA_NAME);
                $name = $this->data($facility, self::RETURNDATA_NAME);
                $region_transformer = new RegionTransformer(self::RETURNDATA_NAME);
                $basic = [
                    'fid' => FacilityIdHash::public($facility->id),
                    'thumb' => $this->facilityThumb($facility),
                    'city' => $city_transformer->transform($facility->city() ?? new City()),
                    'region' => $region_transformer->transform($facility->region() ?? new Region()),
                    'venues_total' => $facility->venues->count()
                ];
                return array_merge($name, $basic);
            case self::RETURNDATA_DETAILS:
                $marker_transformer = new MarkerTransformer(self::RETURNDATA_BASIC);
                $details =  [
                    'marker' => $marker_transformer->transform($facility->marker() ?? new Marker()),
                    'images' => $this->facilityImages($facility),
                    'venues' => $this->facilityVenues($facility),
                ];
                return $details;
            case self::RETURNDATA_NAME:
                return [
                    'name' => [
                        'ar' => $facility->name_ar ?? Facility::DEFAULT_NAME,
                        'en' => $facility->name_en ?? Facility::DEFAULT_NAME,
                    ],
                    'contacts' => [
                        'phone_numbers' => [
                            env('FACILITY_PHONE_NUMBER', '0798495969')
                        ]
                    ]
                ];
            case self::RETURNDATA_NONE:
                return [];
        }

        return [];
    }

    private function facilityVenues(Facility $facility)
    {
        $venue_transformer = new VenueTransformer(self::RETURNDATA_FACILITY_VENUE);
        $venues = $facility->venues;

        $data = [];
        foreach($venues AS $venue){
            $data[] = $venue_transformer->transform($venue);
        }

        return $data;
    }

    private function facilityImages(Facility $facility)
    {
        $image_transformer = new ImageTransformer();
        $images = $facility->images()->get();

        $data = [];
        foreach($images AS $image){
            $data[] = $image_transformer->transform($image);
        }

        return $data;
    }

    private function facilityThumb(Facility $facility)
    {
        $image_transformer = new ImageTransformer();
        $image = $facility->images()->where('type', 1)->first(); //Main Image
        if($image == null){
            $image = $facility->images()->first(); //First Image
        }

        return $image_transformer->transform($image ?? new Image());
    }
}