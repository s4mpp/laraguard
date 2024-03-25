<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

final class ChangePasswordTest extends TestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_change_password($panel): void
    {
        $current_password = 'pa55word';
        $new_password = 'p4ssword';

        $factory = $panel['factory'];
        $user = $factory::new([
            'password' => Hash::make($current_password),
        ])->create();

        $this->get($panel['prefix'].'/minha-conta');
        $response = $this->actingAs($user, $panel['guard_name'])->put($panel['prefix'].'/minha-conta/alterar-senha', [
            'current_password' => $current_password,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($panel['prefix'].'/minha-conta');

        $user->refresh();

        $this->assertTrue(Hash::check($new_password, $user->password));
        $this->assertFalse(Hash::check($current_password, $user->password));
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_change_password_with_invalid_current_password($panel): void
    {
        $current_password = '94ssword';
        $new_password = 'p455word';

        $factory = $panel['factory'];
        $user = $factory::new([
            'password' => Hash::make($current_password),
        ])->create();

        $this->get($panel['prefix'].'/minha-conta');
        $response = $this->actingAs($user, $panel['guard_name'])->put($panel['prefix'].'/minha-conta/alterar-senha', [
            'current_password' => 'anotherpass123',
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $response->assertRedirect($panel['prefix'].'/minha-conta');

        $user->refresh();

        $this->assertFalse(Hash::check($new_password, $user->password));
    }
}
