<?php

namespace S4mpp\Laraguard\Tests\Unit\Concerns;

use RuntimeException;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Concerns\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use S4mpp\Laraguard\Notifications\ResetPassword;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Facades\{Auth, Hash, Notification};
use Workbench\Database\Factories\{CustomerFactory, UserFactory};

final class PasswordTest extends TestCase
{

    /**
     * @dataProvider guardProvider
     */
    public function test_update_password($guard_name, $uri, $factory): void
    {
        $new_password = 'pa55word';

        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        $token = PasswordFacade::broker($guard_name)->createToken($user);

        $status = Password::reset($panel, $user, $token, $new_password);

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

        $status = Password::reset($panel, $user, rand(), $new_password);

        $user->refresh();

        $this->assertSame($status, PasswordBroker::INVALID_TOKEN);

        $this->assertFalse(Hash::check($new_password, $user->password));
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_send_link($guard_name, $uri, $factory): void
    {
        Notification::fake();

        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        $status = Password::sendLinkReset($panel, $user);

        $this->assertSame($status, PasswordBroker::RESET_LINK_SENT);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo([$user], ResetPassword::class);
    }

    /**
     * @dataProvider guardProvider
     */
    public function test_send_link_user_inexistent($guard_name, $uri, $factory): void
    {
        Notification::fake();

        $user = $factory::new()->make();

        $panel = new Panel('', '', $guard_name);

        $status = Password::sendLinkReset($panel, $user);

        $this->assertSame($status, PasswordBroker::INVALID_USER);

        Notification::assertNothingSent([$user], ResetPassword::class);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_render_notification(): void
    {
        $notification = new ResetPassword('https://example.com');

        $mail = $notification->toMail(new \stdClass);

        $this->assertEquals('Reset Password', $mail->subject);
        $this->assertStringContainsString('https://example.com', $mail->actionUrl);
        $this->assertStringContainsString('Reset password', $mail->actionText);
    }
}
