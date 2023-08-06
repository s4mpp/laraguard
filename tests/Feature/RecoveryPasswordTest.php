<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use S4mpp\Laraguard\Mail\PasswordRecoveryMail;

class RecoveryPasswordTest extends TestCase
{
	public function emailInvalidProvider()
    {
        return [
            'vazio' => [''],
            'nulo' => [null],
            'numerico' => [123],
            'string numerico' => ['456'],
            'numeros' => ['João 2'],
            'sem @' => ['Jonas-Teste'],
            '@ no inicio' => ['@gmail.com'],
            'formato invalido' => ['Teset@'],
            '300 caracteres' => [str_repeat('a', 300)],
            '151 caracteres' => [str_repeat('b', 151)],
        ];
    }

    public function test_login_recovery_password(): void
    {
        $response = $this->get('/test/recuperar-senha');

        $response->assertStatus(200);
    }

	public function test_solicitacao_codigo_recuperacao_senha()
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->get('/test/recuperar-senha');
        $response = $this->post('/test/recuperar-senha', ['email' => $user->email]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $response->assertRedirect('/test/recuperar-senha');
        
        Mail::assertQueued(PasswordRecoveryMail::class, function ($mail) use ($user)
        {
            return $mail->hasTo($user->email);
        });
        
        $user->refresh();
        
        $this->assertDatabaseHas('users', [
            'token_password_recovery' => $user->token_password_recovery,
        ]);
    }

	/**
     * @dataProvider emailInvalidProvider
     */
	public function test_solicitacao_codigo_recuperacao_senha_com_email_invalido($email)
    {
        Mail::fake();

        $this->get('/test/recuperar-senha');
        $response = $this->post('/test/recuperar-senha', ['email' => $email]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
        $response->assertRedirect('/test/recuperar-senha');

        Mail::assertNothingQueued(PasswordRecoveryMail::class);
    }
   
    public function test_solicitacao_codigo_recuperacao_senha_com_email_inexistente()
    {
        Mail::fake();

        $this->get('/test/recuperar-senha');
        $response = $this->post('/test/recuperar-senha', ['email' => 'random@abc.com']);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $response->assertRedirect('/test/recuperar-senha');
        
        Mail::assertNothingQueued(PasswordRecoveryMail::class);
    }

    public function test_tela_alteracao_senha()
    {
		$user = User::factory(['token_password_recovery' => md5(rand())])->create();

        $response = $this->get('/test/recuperar-senha/alterar/'.$user->token_password_recovery);

        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
    }

	public function test_acao_alterar_senha()
    {
		$token = md5(rand());

        $user = User::factory(['token_password_recovery' => $token])->create();
		
		$password = '12345678';

        $response = $this->post('/test/recuperar-senha/alterar/'.$token, [
            '_method' => 'PUT',
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/test/entrar');

		$user->refresh();
        
        $this->assertTrue(Hash::check($password, $user->password));
        
        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
            'token_password_recovery' => $token,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'token_password_recovery' => null,
        ]);
    }

    public function test_tela_alteracao_senha_codigo_invalido()
    {
        $this->get('/test/recuperar-senha/alterar/ramdom123');
        $response = $this->get('/test/recuperar-senha/alterar/ramdom123');

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $response->assertRedirect('/test/recuperar-senha');
    }

    public function test_acao_alterar_senha_codigo_invalido()
    {
        $this->get('/test/recuperar-senha/alterar/ramdom123');
        $response = $this->post('/test/recuperar-senha/alterar/ramdom123', [
            '_method' => 'PUT',
            'password' => '123456789',
            'password_confirmation' => '123456789'
        ]);

        $response->assertStatus(404);
    }

    public function test_alteracao_senha_invalida()
    {
		$new_password = 'abcd';

        $token = md5(rand());

        $user = User::factory(['token_password_recovery' => $token])->create();

        $this->get('/test/recuperar-senha/alterar/'.$token);
        $response = $this->post('/test/recuperar-senha/alterar/'.$token, [
            '_method' => 'PUT',
            'password' => $new_password,
            'password_confirmation' => $new_password
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $response->assertRedirect('/test/recuperar-senha/alterar/'.$token);

        $user->refresh();

        $this->assertFalse(Hash::check($new_password, $user->password));
    }
}
