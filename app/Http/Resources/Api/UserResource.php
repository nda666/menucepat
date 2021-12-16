<?php

namespace App\Http\Resources\Api;

use App\Enums\BloodType;
use App\Enums\SexType;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    protected $success;
    public $collects = User::class;

    public static $wrap = 'data';

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
        $data['blood'] = BloodType::getDescription($data['blood']);
        $data['sex'] = SexType::getDescription($data['sex']);
        return $data;
    }

    public function with($request)
    {
        return [
            'success' => $this->success,
        ];
    }
}
