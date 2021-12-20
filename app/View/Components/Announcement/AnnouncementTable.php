<?php

namespace App\View\Components\Announcement;

use Illuminate\View\Component;

class AnnouncementTable extends Component
{
    public $id;
    public $filterFormId;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id = 'datatable', $filterFormId = 'datatable-filter')
    {
        $this->id = $id;
        $this->filterFormId = $filterFormId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.announcement.announcement-table');
    }
}
