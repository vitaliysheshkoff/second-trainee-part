<?php

namespace App\View\Components;

use Illuminate\View\Component;

class User extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public string $class;
    public $user;

    public function __construct($class, $user)
    {
        $this->class = $class;
        $this->user = $user;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.user');
    }
}
