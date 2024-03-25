<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Notifications\ResetPassword;
use Illuminate\Support\Facades\{Auth, Hash, Mail, Notification};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class RecoveryPasswordTest extends TestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_route_index($panel): void
    {
        $response = $this->get($panel['prefix'].'/recuperacao-de-senha');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_send_email($panel): void
    {
        Notification::fake();

        $factory = $panel['factory'];
        $user = $factory::new()->create();

        $this->get($panel['prefix'].'/recuperacao-de-senha');
        $response = $this->post($panel['prefix'].'/recuperacao-de-senha', [
            'email' => $user->email,
        ]);

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/recuperacao-de-senha');

        Notification::assertSentTo([$user], ResetPassword::class);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_request_email_non_existing($panel): void
    {
        Notification::fake();

        $email = 'random.'.rand().'.email.com';

        $this->get($panel['prefix'].'/recuperacao-de-senha');
        $response = $this->post($panel['prefix'].'/recuperacao-de-senha', [
            'email' => $email,
        ]);

        $response->assertSessionHasErrorsIn('default');

        $response->assertStatus(302);
        $response->assertRedirect($panel['prefix'].'/recuperacao-de-senha');

        Notification::assertNothingSent();

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $email,
        ]);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_request_email_of_another_guard($panel): void
    {
        Notification::fake();

        $user = UserFactory::new()->create();

        $this->get('/area-do-cliente/recuperacao-de-senha');
        $response = $this->post('/area-do-cliente/recuperacao-de-senha', [
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrorsIn('default');

        $response->assertStatus(302);
        $response->assertRedirect('/area-do-cliente/recuperacao-de-senha');

        Notification::assertNothingSent();

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }
}
