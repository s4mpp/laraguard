<?php

namespace S4mpp\Laraguard\Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Chrome;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use S4mpp\Laraguard\Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Workbench\Database\Factories\CustomerFactory;

class RegisterTest extends DuskTestCase
{
	/**
	 * @dataProvider panelProvider
	 */
	public function test_register_screen($panel): void
	{
		$this->browse(function (Browser $browser) use ($panel)
		{
			$browser->visit('/customer-area/signin')
				->click('@register-button')
				->assertPathIs('/customer-area/signup')
				->assertTitle('My account | Register')
	
				->assertSee('Nome')
				->assertInputValue('name', '')
				
				->assertSee('E-mail')
				->assertInputValue('email', '')

				->assertSee('Password')
				->assertInputValue('password', '')

				->assertButtonEnabled('Cadastrar')

				->assertSeeLink('Go back');
		});
	}

	public function test_try_register(): void
	{
		$this->browse(function (Browser $browser) 
		{
			$password = '12346578abcd';

			$user = CustomerFactory::new()->make();

			$browser->visit('/customer-area/signup')
				->type('name', $user->name)
				->type('email', $user->email)
				->type('password', $password)
				->press('@register')
				->assertPathIs('/customer-area/signup/user-registered')
				->assertSeeLink('Clique aqui para acessar')
				->press('@click-to-access')
				->assertPathIs('/customer-area/signin');
		});
	}


	




}
