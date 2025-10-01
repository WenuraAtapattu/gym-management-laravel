<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Label extends Component
{
    /**
     * The label text.
     *
     * @var string
     */
    public $value;

    /**
     * Create a new component instance.
     *
     * @param  string  $value
     * @return void
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.label');
    }
}
