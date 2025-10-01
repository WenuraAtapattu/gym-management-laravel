<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputError extends Component
{
    /**
     * The input name.
     *
     * @var string
     */
    public $for;

    /**
     * Create a new component instance.
     *
     * @param  string  $for
     * @return void
     */
    public function __construct($for = null)
    {
        $this->for = $for;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.input-error');
    }
}
