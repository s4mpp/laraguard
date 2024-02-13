<?php

namespace S4mpp\Laraguard\Tests\Unit;

use Workbench\App\Models\User;
use S4mpp\Laraguard\Tests\TestCase;
use S4mpp\Laraguard\Helpers\Credential;
use Workbench\Database\Factories\UserFactory;

final class CredentialTest extends TestCase
{
    public function test_create_instance(): void
    {
        $new_instance = new Credential();

        $this->assertSame('E-mail', $new_instance->getTitle());
        $this->assertSame('email', $new_instance->getField());
    }

    public function test_create_instance_with_parameters(): void
    {
        $new_instance = new Credential('Nome', 'field');

        $this->assertSame('Nome', $new_instance->getTitle());
        $this->assertSame('field', $new_instance->getField());
    }

    public function test_suggest_email(): void
    {
        $email = Credential::suggestEmail(app(User::class), 'John');

        $this->assertIsString($email);
        $this->assertEquals('john@mail.com', $email);
    }

    public function test_suggest_email_existing(): void
    {
        UserFactory::new()->create(['email' => 'taylor@mail.com']);

        $email = Credential::suggestEmail(app(User::class), 'Taylor');

        $this->assertIsString($email);
        $this->assertEquals('taylor1@mail.com', $email);
    }

    public function test_generate_password(): void
    {
        $password = Credential::generatePassword();

        $this->assertIsString($password);
    }
}
