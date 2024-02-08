<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;


class ModuleController extends Controller
{
    public function __invoke(): null | \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory
    {
        return Laraguard::layout();
    }
}
