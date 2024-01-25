<?php

namespace Workbench\App\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;

final class WithdrawalController extends Controller
{
    public function __invoke()
    {
        return Laraguard::layout('withdrawal');
    }
}