<?php

namespace App\Http\Resources\Api;

use App\Enums\BloodType;
use App\Enums\SexType;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{

    protected $success;

    public $collects = Attendance::class;
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
        $data['check_clock'] = $this->check_clock->format('d/m/Y H:i:s');
        $data['duty_on'] = Carbon::parse($this->duty_on)->format('d/m/Y H:i:s');
        $data['duty_off'] = Carbon::parse($this->duty_off)->format('d/m/Y H:i:s');
        return $data;
    }

    public function with($request)
    {
        return [
            'success' => $this->success,
        ];
    }
}
