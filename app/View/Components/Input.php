<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $name;
    public $label;
    public $type;
    public $value;
    public $required;
    public $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name, 
        $label = null, 
        $type = 'text', 
        $value = null, 
        $required = false, 
        $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label ?? ucfirst($name);
        $this->type = $type;
        $this->value = old($name, $value);
        $this->required = $required;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.input');
    }
}