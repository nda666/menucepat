<?php

namespace App\View\Components\Schedule;

use App\Enums\SexType;
use Illuminate\View\Component;

class ScheduleForm extends Component
{

    public $gridId = null;
    public $sexType = null;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($gridId = null)
    {
        $this->gridId = $gridId;

        $this->sexType = SexType::getInstances();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.schedule.schedule-form');
    }
}
