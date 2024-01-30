<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Workbench\Database\Factories\UserFactory;
use S4mpp\Laraguard\Notifications\ResetPassword;
use Workbench\Database\Factories\CustomerFactory;

class RecoveryPasswordTest extends TestCase
{
	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_route_index($guard_name, $uri)
	{
		$response = $this->get('/'.$uri.'/password-recovery');

		$response->assertStatus(200);
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_send_email($guard_name, $uri, $factory)
	{
		Notification::fake();

		$user = $factory::new()->create();

		$this->get('/'.$uri.'/password-recovery');
		$response = $this->post('/'.$uri.'/password-recovery', [
			'email' => $user->email,
		]);

		$response->assertSessionHasNoErrors();

		$response->assertStatus(302);
		$response->assertRedirect('/'.$uri.'/password-recovery');

		Notification::assertSentTo([$user], ResetPassword::class);

		$this->assertDatabaseHas('password_reset_tokens', [
			'email' => $user->email,
		]);
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_request_email_non_existing($guard_name, $uri, $factory)
	{
		Notification::fake();

		$email = 'random.'.rand().'.email.com';
	
		$this->get('/'.$uri.'/password-recovery');
		$response = $this->post('/'.$uri.'/password-recovery', [
			'email' => $email,
		]);

		$response->assertSessionHasErrorsIn('default');

		$response->assertStatus(302);
		$response->assertRedirect('/'.$uri.'/password-recovery');

		Notification::assertNothingSent();

		$this->assertDatabaseMissing('password_reset_tokens', [
			'email' => $email,
		]);
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_request_email_of_another_guard($guard_name, $uri, $factory, $another_guard, $another_guard_url)
	{
		Notification::fake();

		$user = $factory::new()->create();

		$this->get('/'.$another_guard_url.'/password-recovery');
		$response = $this->post('/'.$another_guard_url.'/password-recovery', [
			'email' => $user->email,
		]);

		$response->assertSessionHasErrorsIn('default');

		$response->assertStatus(302);
		$response->assertRedirect('/'.$another_guard_url.'/password-recovery');

		Notification::assertNothingSent();

		$this->assertDatabaseMissing('password_reset_tokens', [
			'email' => $user->email,
		]);
	}

}