<?php

namespace S4mpp\Laraguard\Tests\Feature\Commands;

use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Factories\UserFactory;

final class MakeUserTest extends TestCase
{
    public static function nameInvalidProvider()
    {
        return [
            'numeric' => [123],
            'string numeric' => ['456'],
            'numbers in string' => ['João 2'],
            'no space' => ['Jonas-Teste'],
            'name as email' => ['Teset@teste'],
            'large' => [str_repeat('a', 300)],
            'limit_max_characters' => [str_repeat('b', 151)],
            'limit_min_characters' => [str_repeat('b', 2)],
            'small' => ['b'],
        ];
    }

    public static function emailInvalidProvider()
    {
        return [
            'numeric' => [123],
            'string numeric' => ['456'],
            'numbers in string' => ['João 2'],
            'sem @' => ['Jonas-Teste'],
            '@ no inicio' => ['@gmail.com'],
            'formato invalido' => ['Teset@'],
        ];
    }

    public function test_make_user(): void
    {
        $this->artisan('laraguard:make-user')
            ->expectsOutput('User created successfully:')
            ->expectsOutputToContain('Name:')
            ->expectsOutputToContain('E-mail: user@mail.com')
            ->expectsOutputToContain('Password:')
            ->expectsOutputToContain('URL:')
            ->assertSuccessful();
            
        $this->assertDatabaseHas('users', ['email' => 'user@mail.com']);
    }

    public function test_make_user_with_invalid_guard(): void
    {
        $this->artisan('laraguard:make-user', ['--guard' => 'xxxx'])
            ->expectsOutput('Invalid guard/panel')
            ->assertSuccessful();
    }

    public function test_make_user_suggest_email_existing(): void
    {
        UserFactory::new()->create(['email' => 'user@mail.com']);

        $this->artisan('laraguard:make-user')
            ->expectsOutputToContain('E-mail: user1@mail.com')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'user1@mail.com']);
    }

    public function test_make_user_generation_password(): void
    {
        Config::set('app.env', 'production');

        $this->artisan('laraguard:make-user')
            ->expectsOutputToContain('Password:')
            ->assertSuccessful();
    }

    /**
     * @dataProvider nameInvalidProvider
     */
    public function test_make_user_with_invalid_name($name): void
    {
        $this->artisan('laraguard:make-user', ['--name' => $name])
            ->doesntExpectOutput('User created successfully:')
            ->assertSuccessful();
    }

    /**
     * @dataProvider emailInvalidProvider
     */
    public function test_make_user_with_invalid_email($email): void
    {
        $this->artisan('laraguard:make-user', ['--email' => $email])
            ->doesntExpectOutputToContain('User created successfully:')
            ->assertSuccessful();
    }
}
