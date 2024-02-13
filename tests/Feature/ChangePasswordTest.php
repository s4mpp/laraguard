<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

final class ChangePasswordTest extends TestCase
{
    /**
     * @dataProvider guardProvider
     */
    public function test_change_password($guard_name, $uri, $factory): void
    {
        $current_password = 'pa55word';
        $new_password = 'p4ssword';

        $user = $factory::new([
            'password' => Hash::make($current_password),
        ])->create();

        $this->get('/'.$uri.'/my-account');
        $response = $this->actingAs($user, $guard_name)->put('/'.$uri.'/my-account/change-password', [
            'current_password' => $current_password,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/'.$uri.'/my-account');

        $user->refresh();

        $this->assertTrue(Hash::check($new_password, $user->password));
        $this->assertFalse(Hash::check($current_password, $user->password));
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_change_password_with_invalid_current_password($guard_name, $uri, $factory): void
    {
        $current_password = '94ssword';
        $new_password = 'p455word';

        $user = $factory::new([
            'password' => Hash::make($current_password),
        ])->create();

        $this->get('/'.$uri.'/my-account');
        $response = $this->actingAs($user, $guard_name)->put('/'.$uri.'/my-account/change-password', [
            'current_password' => 'anotherpass123',
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $response->assertRedirect('/'.$uri.'/my-account');

        $user->refresh();

        $this->assertFalse(Hash::check($new_password, $user->password));
    }
}
