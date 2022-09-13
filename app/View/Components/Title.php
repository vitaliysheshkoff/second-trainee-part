<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Title extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public string $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.title');
    }
}
