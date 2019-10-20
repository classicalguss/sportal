<?php

namespace App\Api\V1\Transformers;

use App\City;
use App\Facility;
use App\Hashes\TypeIdHash;
use App\Hashes\VenueIdHash;
use App\Image;
use App\Marker;
use App\Region;
use App\Venue;
use App\Type;

class VenueTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Venue $venue
     * @return array
     */
    public function transform(Venue $venue)
    {
        $venue_data = $this->data($venue, $this->getReturnData());

        if($venue->extra_data){
            foreach($venue->extra_data AS $key => $value){
                $data[$key] = $value;
            }
            $data['venue'] = $venue_data;
        } else {
            $data = $venue_data;
        }

        return $data;
    }

    private function data(Venue $venue, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($venue, self::RETURNDATA_BASIC);
                $details = $this->data($venue, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                $facility_transformer = new FacilityTransformer(self::RETURNDATA_NAME);
                $city_transformer = new CityTransformer(self::RETURNDATA_NAME);
                $region_transformer = new RegionTransformer(self::RETURNDATA_NAME);
                $marker_transformer = new MarkerTransformer();
                $basic = [
                    'vid' => VenueIdHash::public($venue->id),
                    'name' => [
                        'ar' => $venue->name_ar ?? Venue::DEFAULT_NAME,
                        'en' => $venue->name_en,
                    ],
                    'thumb' => $this->venueThumb($venue),
                    'facility' => $facility_transformer->transform($venue->facility() ?? new Facility()),
                    'city' => $city_transformer->transform($venue->city() ?? new City()),
                    'region' => $region_transformer->transform($venue->region() ?? new Region()),
                    'max_players' => $venue->max_players ?? Venue::DEFAULT_MAX_PLAYERS,
                    'marker' => $marker_transformer->transform($venue->marker() ?? new Marker()),
                    'address' => [
                        'name' => [
                            'ar' => $venue->address_ar ?? Venue::DEFAULT_ADDRESS,
                            'en' => $venue->address_en ?? Venue::DEFAULT_ADDRESS,
                        ]
                    ]
                ];
                return $basic;
            case self::RETURNDATA_DETAILS:
                $type_transformer = new TypeTransformer();
                $types = $venue->types()->get();
                $venue_types = [];
                foreach($types AS $type){
                    $venue_types[] = $type_transformer->transform($type);
                }
                $details = [
                    'rate' => $venue->rate ?? Venue::DEFAULT_RATE,
                    'indoor' => $venue->indoor ?? Venue::DEFAULT_INDOOR,
                    'rules' => $venue->rules ?? Venue::DEFAULT_RULES,
                    'price' => $venue->price ?? Venue::DEFAULT_PRICE,
                    'venue_type' => $type_transformer->transform($venue->type() ?? new Type()),
                    'venue_types' => $venue_types,
                    'images' => $this->venueImages($venue),
                    'interval_enable' => $venue->interval_enable,
                    'interval_times' => isset($venue->interval_times) ? json_decode($venue->interval_times)->minutes : [],
                ];
                return $details;
            case self::RETURNDATA_FACILITY_VENUE:
                $facility_venue = $this->data($venue, self::RETURNDATA_BASIC);
                $facility_venue['price'] = $venue->price ?? Venue::DEFAULT_PRICE;
                $facility_venue['rate'] = $venue->rate ?? Venue::DEFAULT_RATE;
                return $facility_venue;
                break;
            case self::RETURNDATA_MARKER:
                $facility_transformer = new FacilityTransformer(self::RETURNDATA_NAME);
                $marker_transformer = new MarkerTransformer(self::RETURNDATA_BASIC);
                $venue_type = $venue->types()->first();
                $basic = [
                    'vid' => VenueIdHash::public($venue->id),
                    'name' => [
                        'ar' => $venue->name_ar ?? Venue::DEFAULT_NAME,
                        'en' => $venue->name_en,
                    ],
                    'facility' => $facility_transformer->transform($venue->facility() ?? new Facility()),
                    'marker' => $marker_transformer->transform($venue->marker() ?? new Marker()),
                    'venue_types' => [[
                            'vtid' => TypeIdHash::public($venue_type->id)
                        ]
                    ]
                ];
                return $basic;
            case self::RETURNDATA_NONE:
                return [];
        }

        return [];
    }

    private function venueImages(Venue $venue)
    {
        $image_transformer = new ImageTransformer();
        $images = $venue->images()->get();

        $data = [];
        foreach($images AS $image){
            $data[] = $image_transformer->transform($image);
        }

        return $data;
    }

    private function venueThumb(Venue $venue)
    {
        $image_transformer = new ImageTransformer();
        $image = $venue->images()->where('type', 1)->first(); //Main Image
        if($image == null){
            $image = $venue->images()->first(); //First Image
        }

        return $image_transformer->transform($image ?? new Image());
    }
}