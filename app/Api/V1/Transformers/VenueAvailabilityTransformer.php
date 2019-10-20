<?php

namespace App\Api\V1\Transformers;

use App\Hashes\VenueAvailabilityIdHash;
use App\Venue;
use App\VenueAvailability;

class VenueAvailabilityTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param VenueAvailability $venue_availability
     * @return array
     */
    public function transform(VenueAvailability $venue_availability)
    {
        $venue_data = $this->data($venue_availability, $this->getReturnData());

        if($venue_availability->extra_data){
            foreach($venue_availability->extra_data AS $key => $value){
                $data[$key] = $value;
            }
            $data['venue_availability'] = $venue_data;
        } else {
            $data = $venue_data;
        }

        return $data;
    }

    private function data(VenueAvailability $venue_availability, $return_data)
    {
        switch ($return_data){
            case self::RETURNDATA_FULL:
                $basic = $this->data($venue_availability, self::RETURNDATA_BASIC);
                $details = $this->data($venue_availability, self::RETURNDATA_DETAILS);
                return array_merge($basic, $details);
            case self::RETURNDATA_BASIC:
                return [
                    'vaid' => $venue_availability->publicId(),
                    'status' => VenueAvailability::$status[$venue_availability->status] ?? VenueAvailability::DEFAULT_STATUS,
                    'date' => $venue_availability->date ?? VenueAvailability::DEFAULT_DATE,
                    'time_start' => $venue_availability->time_start ?? VenueAvailability::DEFAULT_TIME,
                    'time_finish' => $venue_availability->time_finish ?? VenueAvailability::DEFAULT_TIME,
                    'duration' => $venue_availability->duration ?? VenueAvailability::DEFAULT_TIME,
                    'price' => $venue_availability->price ?? VenueAvailability::DEFAULT_PRICE,
                    'notes' => $venue_availability->notes ?? VenueAvailability::DEFAULT_NOTES
                ];
            case self::RETURNDATA_DETAILS:
                $venue_transformer = new VenueTransformer(self::RETURNDATA_FULL);
                return [
                    'venue' => $venue_transformer->transform($venue_availability->venue() ?? new Venue())
                ];
            case self::RETURNDATA_NONE:
                return [];
        }

        return [];
    }
}