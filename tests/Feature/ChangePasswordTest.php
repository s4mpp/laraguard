<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\UserFactory;

final class ChangePasswordTest extends TestCase
{
    public function test_index_page(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user, 'web')->get('/area-restrita/minha-conta/alterar-senha');

        $response->assertStatus(200);
    }
    
    public function test_change_password(): void
    {
        $current_password = 'pa55word';
        $new_password = 'p4ssword';

        $user = UserFactory::new([
            'password' => Hash::make($current_password),
        ])->create();

        $this->get('area-restrita/minha-conta/alterar-senha');
        $response = $this->actingAs($user, 'web')->put('area-restrita/minha-conta/alterar-senha/salvar-senha', [
            'current_password' => $current_password,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('area-restrita/minha-conta/alterar-senha');

        $user->refresh();

        $this->assertTrue(Hash::check($new_password, $user->password));
        $this->assertFalse(Hash::check($current_password, $user->password));
    }

    
    public function test_change_password_with_invalid_current_password(): void
    {
        $current_password = '94ssword';
        $new_password = 'p455word';

        $user = UserFactory::new([
            'password' => Hash::make($current_password),
        ])->create();

        $this->get('area-restrita/minha-conta/alterar-senha');
        $response = $this->actingAs($user, 'web')->put('area-restrita/minha-conta/alterar-senha/salvar-senha', [
            'current_password' => 'anotherpass123',
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $response->assertRedirect('area-restrita/minha-conta/alterar-senha');

        $user->refresh();

        $this->assertFalse(Hash::check($new_password, $user->password));
    }
}
