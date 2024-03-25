<?php

namespace S4mpp\Laraguard\Tests;

use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

trait InteractsWithPanels
{
	public static function panelProvider(): array
    {
        return [
            'Web' => [[
				'title' => 'Área Restrita',
                'guard_name' => 'web',
                'factory' => UserFactory::class,
                'prefix' => 'area-restrita',
                'redirect_to_after_login' => 'home',
				'can_register' => false
            ]],
            'Customer' => [[
				'title' => 'Área do cliente',
                'guard_name' => 'customer',
                'factory' => CustomerFactory::class,
                'prefix' => 'area-do-cliente',
                'redirect_to_after_login' => 'minha-conta',
				'can_register' => true
            ]],
        ];
    }
}