<?php

namespace App\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class PegawaiSelect2 extends Component
{
    public $id;
    public $label;
    public $name;
    public $fgroupClass;
    public $isgroupClass;
    public $labelClass;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id = '', $name = "user_id", $label = '', $fgroupClass = '', $isgroupClass = '', $labelClass = '')
    {
        $this->id = $id ? $id : Str::createUuidsNormally();
        $this->label = $label ? $label : 'Pegawai';
        $this->fgroupClass = $fgroupClass;
        $this->isgroupClass = $isgroupClass;
        $this->labelClass = $labelClass;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.pegawai-select2');
    }
}
