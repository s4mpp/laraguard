<?php

namespace Workbench\App\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Customer extends User
{
    use HasFactory, Notifiable;
}
