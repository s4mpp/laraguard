<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Laraguard;


class PageController extends Controller
{
    public function __invoke()
    {
        return Laraguard::layout();
    }
}
