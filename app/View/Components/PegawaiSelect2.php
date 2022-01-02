<?php

namespace App\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class PegawaiSelect2 extends Component
{
    public $id;
    public $label;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id = '', $label = '')
    {
        $this->id = $id ? $id : Str::createUuidsNormally();
        $this->label = $label ? $label : 'Pegawai';
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
