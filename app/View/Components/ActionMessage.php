<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ActionMessage extends Component
{
    /**
     * The event to listen for.
     *
     * @var string
     */
    public $on;

    /**
     * Create a new component instance.
     *
     * @param  string  $on
     * @return void
     */
    public function __construct($on = null)
    {
        $this->on = $on;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.action-message');
    }
}
