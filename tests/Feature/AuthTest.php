<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    public function test_login_screen(): void
    {
        $response = $this->get('/test/entrar');

        $response->assertStatus(200);
    }

    public function test_tentativa_login_correto()
    {
        $password = Str::password();

        $user = User::factory(['password' => Hash::make($password)])->create();

        $response = $this->post('/test/entrar', ['email' => $user->email, 'password' => $password]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }
    
    public function test_tentativa_login_dados_errados()
    {
        $user = User::factory()->create();
        
        $response = $this->post('/test/entrar', [
            'email' => $user->email,
            'password' => md5(rand())
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect('/test/entrar');
       
        $this->assertEquals(Auth::user(), null);
    }

    public function test_tentativa_login_email_inexistente()
    {
        $response = $this->post('/test/entrar', [
            'email' => 'emailnexistente.'.rand().'@ramdom'.rand().'.com.br',
            'password' => md5(rand())
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect('/test/entrar');
       
        $this->assertEquals(Auth::user(), null);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/test/sair');

        $response->assertStatus(302);
        $response->assertRedirect('/test/entrar');

        $this->assertEquals(Auth::guard('admin')->user(), null);
    }

}
