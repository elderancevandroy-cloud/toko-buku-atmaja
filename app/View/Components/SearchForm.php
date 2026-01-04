<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchForm extends Component
{
    public $searchFields;
    public $filters;
    public $action;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($searchFields = [], $filters = [], $action = '')
    {
        $this->searchFields = $searchFields;
        $this->filters = $filters;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.search-form');
    }
}