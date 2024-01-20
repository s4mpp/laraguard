<?php

namespace S4mpp\Laraguard\Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

class PasswordRecoveryTest extends TestCase
{
	public static function guardProvider()
	{
		return [
			'Web' => ['web', 'restricted-area', UserFactory::class, 'customer', 'customer-area'],
			'Customer' => ['customer',  'customer-area', CustomerFactory::class, 'web', 'restricted-area'],
		];
	}

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
	}

	/**
	 *
	 * @dataProvider guardProvider
	 */
	public function test_request_email_non_existing($guard_name, $uri, $factory)
	{
		Notification::fake();
	
		$this->get('/'.$uri.'/password-recovery');
		$response = $this->post('/'.$uri.'/password-recovery', [
			'email' => 'random.'.rand().'.email.com',
		]);

		$response->assertSessionHasErrorsIn('default');

		$response->assertStatus(302);
		$response->assertRedirect('/'.$uri.'/password-recovery');

		Notification::assertNothingSent();
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
	}

}