<?php

namespace App\View\Components\Attendance;

use App\Enums\ClockType;
use App\Enums\SexType;
use Illuminate\View\Component;

class AttendanceForm extends Component
{

    public $gridId = null;
    public $clockType = null;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($gridId = null)
    {
        $this->gridId = $gridId;

        $this->clockType = ClockType::asSelectArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.attendance.attendance-form');
    }
}
