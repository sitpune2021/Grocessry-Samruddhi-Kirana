<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Pagination extends Component
{
    public $from;
    public $to;
    public $total;

    public function __construct($from = 1, $to = 10, $total = 0)
    {
        $this->from  = $from;
        $this->to    = $to;
        $this->total = $total;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pagination');
    }
}
