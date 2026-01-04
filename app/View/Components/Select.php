<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $required;
    public $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name, 
        $options = [], 
        $label = null, 
        $selected = null, 
        $required = false, 
        $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label ?? ucfirst($name);
        $this->options = $options;
        $this->selected = old($name, $selected);
        $this->required = $required;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.select');
    }
}