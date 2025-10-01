<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DialogModal extends Component
{
    /**
     * The modal's title.
     *
     * @var string
     */
    public $title;

    /**
     * The modal's content.
     *
     * @var string
     */
    public $content;

    /**
     * The modal's footer.
     *
     * @var string
     */
    public $footer;

    /**
     * The modal's width.
     *
     * @var string
     */
    public $maxWidth;

    /**
     * Create a new component instance.
     *
     * @param  string  $title
     * @param  string  $content
     * @param  string  $footer
     * @param  string  $maxWidth
     * @return void
     */
    public function __construct($title = null, $content = null, $footer = null, $maxWidth = '2xl')
    {
        $this->title = $title;
        $this->content = $content;
        $this->footer = $footer;
        $this->maxWidth = $maxWidth;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.dialog-modal');
    }
}
