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
            $browser->visit('/area-do-cliente/entrar')
                ->click('@register-button')
                ->assertPathIs('/area-do-cliente/cadastro')
                ->assertTitleContains('Área do cliente | Register')

                ->assertSee('Nome')
                ->assertInputValue('name', '')

                ->assertSee('E-mail')
                ->assertInputValue('email', '')

                ->assertSee('Senha')
                ->assertInputValue('password', '')

                ->assertButtonEnabled('Cadastrar')

                ->assertSeeLink('Voltar');
        });
    }

    public function test_try_register(): void
    {
        $this->browse(function (Browser $browser): void {
            $password = '12346578abcd';

            $user = CustomerFactory::new()->make();

            $browser->visit('/area-do-cliente/cadastro')
                ->type('name', $user->name)
                ->type('email', $user->email)
                ->type('password', $password)
                ->press('@register')
                ->assertPathIs('/area-do-cliente/cadastro/finalizado')
                ->assertSeeLink('Clique aqui para acessar')
                ->press('@click-to-access')
                ->assertPathIs('/area-do-cliente/entrar');
        });
    }
}
