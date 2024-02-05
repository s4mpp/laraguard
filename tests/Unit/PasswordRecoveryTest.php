<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Illuminate\Auth\Notifications\ResetPassword as NotificationsResetPassword;
use RuntimeException;
use S4mpp\Laraguard\Base\Panel;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use S4mpp\Laraguard\Navigation\MenuItem;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Notifications\Messages\MailMessage;
use Workbench\Database\Factories\UserFactory;
use S4mpp\Laraguard\Notifications\ResetPassword;
use stdClass;
use Workbench\Database\Factories\CustomerFactory;

class PasswordRecoveryTest extends TestCase
{	
	/**
	 * @dataProvider guardProvider
	 */
	public function test_send_link($guard_name, $uri, $factory)
	{
		Notification::fake();

		$user = $factory::new()->create();

		$panel = new Panel('', '', $guard_name);

		$status = $panel->sendLinkRecoveryPassword($user);

		$this->assertSame($status, PasswordBroker::RESET_LINK_SENT);

		$this->assertDatabaseHas('password_reset_tokens', [
			'email' => $user->email,
		]);
		
		Notification::assertSentTo([$user], ResetPassword::class);
	}


	/**
	 * @dataProvider guardProvider
	 */
	public function test_send_link_user_inexistent($guard_name, $uri, $factory)
	{
		Notification::fake();

		$user = $factory::new()->make();

		$panel = new Panel('', '', $guard_name);

		$status = $panel->sendLinkRecoveryPassword($user);

		$this->assertSame($status, PasswordBroker::INVALID_USER);

		Notification::assertNothingSent([$user], ResetPassword::class);

		$this->assertDatabaseMissing('password_reset_tokens', [
			'email' => $user->email,
		]);
	}

	public function test_render_notification()
	{
		$notification = new ResetPassword('https://example.com');

		$mail = $notification->toMail(new stdClass);

		$this->assertEquals('Reset Password Notification', $mail->subject);
		$this->assertStringContainsString('https://example.com', $mail->actionUrl);
		$this->assertStringContainsString('Reset Password', $mail->actionText);
	}
}