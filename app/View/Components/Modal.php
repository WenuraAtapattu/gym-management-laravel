<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * The modal's ID.
     *
     * @var string
     */
    public $id;

    /**
     * The modal's maximum width.
     *
     * @var string
     */
    public $maxWidth;

    /**
     * Create a new component instance.
     *
     * @param  string  $id
     * @param  string  $maxWidth
     * @return void
     */
    public function __construct($id = null, $maxWidth = '2xl')
    {
        $this->id = $id ?: 'modal-' . md5(uniqid());
        $this->maxWidth = $maxWidth;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
