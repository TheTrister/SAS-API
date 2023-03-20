<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ToJsonResourceDouble extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $resourceSec;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @return void
     */
    public function __construct($status, $message, $resource, $resourceSec)
    {
        parent::__construct($resource);
        // parent::__construct($resourceSec);
        $this->resourceSec  = $resourceSec;
        $this->status  = $status;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success'   => $this->status,
            'message'   => $this->message,
            'data_first'      => $this->resource,
            'data_second' => $this->resourceSec
        ];
    }
}
