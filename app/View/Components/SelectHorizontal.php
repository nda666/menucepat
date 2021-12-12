<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectHorizontal extends Component
{
    public $id;
    public $label;
    public $labelClass;
    public $fgroupClass;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $id,
        $fGroupClass = "",
        $label = "",
        $labelClass = "",
        $fgroupClass = ""
    ) {
        $this->id = $id;
        $this->fGroupClass = $fGroupClass;
        $this->label = $label;
        $this->labelClass = $labelClass;
        $this->fgroupClass = $fgroupClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.select-horizontal');
    }
}
