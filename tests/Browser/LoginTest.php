<?php

namespace S4mpp\Laraguard\Tests\Browser;

use Laravel\Dusk\{Browser, Chrome};
use S4mpp\Laraguard\Tests\DuskTestCase;
use Illuminate\Support\Facades\{Config, DB, Hash};

final class LoginTest extends DuskTestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_login_screen($panel): void
    {
        $this->browse(function (Browser $browser) use ($panel): void {
            $browser->visit($panel['prefix'])
                ->assertTitleContains($panel['title'].' | Sign In')
                ->assertPathIs('/'.$panel['prefix'].'/entrar')

                ->assertInputValue('username', '')
                ->assertInputValue('password', '')
                ->assertButtonEnabled('Login')

                ->assertSeeLink('Lost your password?');

            if ($panel['can_register']) {
                $browser->assertVisible('@register-call');
                $browser->assertSeeLink('Cadastre-se');
            } else {
                $browser->assertMissing('@register-call');
                $browser->assertDontSeeLink('Cadastre-se');
            }
        });
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_try_login($panel): void
    {
        $this->browse(function (Browser $browser) use ($panel): void {
            $password = '12345678910';

            $factory = $panel['factory'];
            $user = $factory::new()->create(['password' => Hash::make($password)]);

            $browser->visit($panel['prefix'])
                ->type('username', $user->email)
                ->type('password', $password)
                ->press('@login')
                ->assertPathIs('/'.$panel['prefix'].'/'.$panel['redirect_to_after_login']);
        });
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_try_login_with_invalid_password($panel): void
    {
        $this->browse(function (Browser $browser) use ($panel): void {
            
            $factory = $panel['factory'];
            $user = $factory::new()->create(['password' => Hash::make('123456789')]);

            $browser->visit($panel['prefix'])
                ->type('username', $user->email)
                ->type('password', 'another-password')
                ->press('@login')
                ->assertPathIs('/'.$panel['prefix'].'/entrar')
                ->assertSee('Credenciais inválidas. Tente novamente')
                ->assertInputValue('username', $user->email)
                ->assertInputValue('password', '');
        });
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_account_not_found($panel): void
    {
        $this->browse(function (Browser $browser) use ($panel): void {
            $browser->visit('/'.$panel['prefix'])
                ->type('username', 'email@email.com')
                ->type('password', '1234')
                ->press('@login')
                ->assertPathIs('/'.$panel['prefix'].'/entrar')
                ->assertSee('Conta não encontrada. Verifique os dados informados ')
                ->assertInputValue('username', 'email@email.com')
                ->assertInputValue('password', '');
        });
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_login_with_empty_password($panel): void
    {
        $this->browse(function (Browser $browser) use ($panel): void {
            $browser->visit('/'.$panel['prefix'])->script("document.querySelector('form').noValidate = true");

            $browser->press('@login')
                ->assertSee('The E-mail field is required.')
                ->assertSee('The Password field is required.')
                ->assertInputValue('username', '')
                ->assertInputValue('password', '');
        });
    }
}
