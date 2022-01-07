<?php

namespace App\Http\Resources\Api;

use App\Enums\BloodType;
use App\Enums\SexType;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \App\Models\Family  $resource
     * @return array
     */
    public $resource;

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
        $data = parent::toArray($request);
        $data['sex'] = SexType::getDescription($data['sex']);
        return $data;
    }
}
