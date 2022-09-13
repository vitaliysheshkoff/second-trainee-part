<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public string $action;
    public string $buttonText;
    public string $method;


    public function __construct($method, $action, $buttonText)
    {
        $this->method = $method;
        $this->buttonText = $buttonText;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */

    public function render()
    {
        return view('components.user-form');
    }
}
