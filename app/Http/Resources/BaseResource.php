<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected $success = true;

    public function __construct($resource, $success = true)
    {
        $this->resource = $resource;
        $this->success = $success;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'success' => $this->success,
            'data' => parent::toArray($request),
        ];
    }
}
