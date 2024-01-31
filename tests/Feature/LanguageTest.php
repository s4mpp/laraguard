<?php

namespace S4mpp\Laraguard\Tests\Feature;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\UserFactory;
use Workbench\Database\Factories\CustomerFactory;

class LanguageTest extends TestCase
{
	public static function localeProvider()
	{
		return [
			'english' => ['en', 'Sign In'],
			'portuguese' => ['pt-BR', 'Login'],
		];
	}

	/**
	 * @dataProvider localeProvider
	 */
	public function test_view_login(string $locale, string $text)
	{
		$this->app->setLocale($locale);

		$response = $this->get('/restricted-area/signin');

		$response->assertStatus(200);

		$response->assertSeeText($text);
	}
}