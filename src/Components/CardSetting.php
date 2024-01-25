<?php

namespace S4mpp\MyAccount\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardSetting extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $title, public string $subtitle = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('my-account::components.card-setting');
    }
}
