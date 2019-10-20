<?php

namespace App\Api\V1\Transformers;

use App\Hashes\TypeIdHash;
use App\Image;
use App\Type;

class TypeTransformer extends BasicTransformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param Type $type
     * @return array
     */
    public function transform(Type $type)
    {
        $data = $this->data($type, $this->getReturnData());
        return $data;
    }

    private function data(Type $type, $return_data)
    {
        $image_transformer = new ImageTransformer();
        return [
            'vtid' => TypeIdHash::public($type->id),
            'image' => $image_transformer->transform($type->image() ?? new Image()),
            'name' => [
                'ar' => $type->name_ar ?? Type::DEFAULT_NAME,
                'en' => $type->name_en ?? Type::DEFAULT_NAME,
            ],
            'color' => $type->color ?? Type::DEFAULT_COLOR
        ];
    }
}