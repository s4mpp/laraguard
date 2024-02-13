<?php

namespace S4mpp\Laraguard\Tests\Unit;

use stdClass;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Auth\Passwords\PasswordBroker;
use S4mpp\Laraguard\Notifications\ResetPassword;
use Workbench\Database\Factories\{CustomerFactory, UserFactory};
use Illuminate\Support\Facades\{Auth, Hash, Mail, Notification, Password};

final class RecoveryPasswordTest extends TestCase
{
    /**
     * @dataProvider guardProvider
     */
    public function test_send_link($guard_name, $uri, $factory): void
    {
        Notification::fake();

        $user = $factory::new()->create();

        $panel = new Panel('', '', $guard_name);

        $status = $panel->auth()->sendLinkResetPassword($user);

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

        $status = $panel->auth()->sendLinkResetPassword($user);

        $this->assertSame($status, PasswordBroker::INVALID_USER);

        Notification::assertNothingSent([$user], ResetPassword::class);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_render_notification(): void
    {
        $notification = new ResetPassword('https://example.com');

        $mail = $notification->toMail(new stdClass);

        $this->assertEquals('Reset Password', $mail->subject);
        $this->assertStringContainsString('https://example.com', $mail->actionUrl);
        $this->assertStringContainsString('Reset password', $mail->actionText);
    }
}
