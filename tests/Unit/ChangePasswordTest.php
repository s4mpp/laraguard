<?php

namespace S4mpp\Laraguard\Tests\Unit;

use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Auth\Passwords\PasswordBroker;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};
use Illuminate\Support\Facades\{Auth, Hash, Notification, Password};

final class ChangePasswordTest extends TestCase
{
    /**
     * @dataProvider guardProvider
     */
    public function test_update_password($guard_name, $uri, $factory): void
    {
        $new_password = 'pa55word';

        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        $token = Password::broker($guard_name)->createToken($user);

        $status = $panel->auth()->resetPassword($user, $token, $new_password);

        $user->refresh();

        $this->assertTrue(Hash::check($new_password, $user->password));

        $this->assertSame($status, PasswordBroker::PASSWORD_RESET);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_update_password_with_invalid_token($guard_name, $uri, $factory): void
    {
        $original_password = 'passw0rd';

        $new_password = 'pa55word';

        $user = $factory::new(['password' => Hash::make($original_password)])->create();

        $panel = new Panel('', '', $guard_name);

        $status = $panel->auth()->resetPassword($user, rand(), $new_password);

        $user->refresh();

        $this->assertSame($status, PasswordBroker::INVALID_TOKEN);

        $this->assertFalse(Hash::check($new_password, $user->password));
    }
}
