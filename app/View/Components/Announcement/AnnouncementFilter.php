<?php

namespace App\View\Components\Announcement;

use Illuminate\View\Component;

class AnnouncementFilter extends Component
{
    public $id;

    public $onSubmit;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id = 'announcement-filter', $onSubmit = null)
    {
        $this->id = $id;
        $this->onSubmit = $onSubmit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.announcement.announcement-filter');
    }
}
