<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\AppOptionsRequest;
use App\Api\V1\Transformers\BasicTransformer;
use App\Api\V1\Transformers\OptionTransformer;
use App\Option;
use Auth;

class AppController extends BaseController
{
    /**
     * Get App Options
     *
     * @param AppOptionsRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function options(AppOptionsRequest $request)
    {
        $return_data = $request->has('return_data') ? $request->input('return_data') : BasicTransformer::RETURNDATA_FULL;

        $options = Option::all();

        return $this->response->collection($options, new OptionTransformer($return_data))->setMeta($this->metaData());
    }
}
