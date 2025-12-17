<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ActionButtons extends Component
{
    public $viewUrl;
    public $editUrl;
    public $deleteUrl;

    public function __construct($viewUrl = null, $editUrl = null, $deleteUrl = null)
    {
        $this->viewUrl   = $viewUrl;
        $this->editUrl   = $editUrl;
        $this->deleteUrl = $deleteUrl;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.action-buttons');
    }
}
