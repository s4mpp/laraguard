<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class ModuleController extends Controller
{
    public function __invoke()
    {
        return Laraguard::layout();
    }
}