<?php

namespace App\View\Components\User;

use App\Enums\BloodType;
use App\Enums\SexType;
use Illuminate\View\Component;

class UserForm extends Component
{

    public $gridId = null;

    public $sexType = [];

    public $bloodType = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($gridId = null)
    {
        $this->gridId = $gridId;
        $this->sexType = SexType::getInstances();
        $this->bloodType = BloodType::getInstances();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.user.user-form');
    }
}
