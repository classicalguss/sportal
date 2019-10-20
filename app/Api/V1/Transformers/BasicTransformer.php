<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;

class BasicTransformer extends TransformerAbstract
{
    /**
     * Return Data
     */
    const RETURNDATA_FULL = 'full';
    const RETURNDATA_BASIC = 'basic';
    const RETURNDATA_DETAILS = 'details';
    const RETURNDATA_NAME = 'name';
    const RETURNDATA_MARKER = 'marker';
    const RETURNDATA_NONE = 'none';

    const RETURNDATA_FACILITY_VENUE = 'facility_venue';

    private $return_data;

    public function __construct($return_data = self::RETURNDATA_BASIC)
    {
        $this->return_data = $return_data;
    }

    public function setReturnData($return_data)
    {
        $this->return_data = $return_data;
    }

    public function getReturnData()
    {
        return $this->return_data;
    }
}