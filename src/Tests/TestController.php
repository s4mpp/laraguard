<?php

namespace S4mpp\Laraguard\Tests;

use App\Http\Controllers\Controller;
use S4mpp\Laraguard\Traits\Authentication;
use S4mpp\Laraguard\Traits\RecoveryPassword;

class TestController extends Controller
{
   use Authentication, RecoveryPassword;

   public $check_status = false;
}