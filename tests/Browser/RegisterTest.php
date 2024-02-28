<?php

namespace S4mpp\Laraguard\Tests\Browser;

use Laravel\Dusk\{Browser, Chrome};
use S4mpp\Laraguard\Tests\DuskTestCase;
use Workbench\Database\Factories\CustomerFactory;
use Illuminate\Support\Facades\{Config, DB, Hash};

final class RegisterTest extends DuskTestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_register_screen(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/customer-area/signin')
                ->click('@register-button')
                ->assertPathIs('/customer-area/signup')
                ->assertTitleContains('Customer area | Register')

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
        $this->browse(function (Browser $browser): void {
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
