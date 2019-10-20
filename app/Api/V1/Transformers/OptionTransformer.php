<?php

namespace App\Api\V1\Transformers;

use App\Option;
use Config;

class OptionTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Option $option
     * @return array
     */
    public function transform(Option $option)
    {
        $data = $this->data($option, $this->getReturnData());
        return $data;
    }

    private function data(Option $option, $return_data)
    {
        return [
            $option['key'] => $option['value']
        ];
    }
}