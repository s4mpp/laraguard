<?php

namespace S4mpp\Laraguard\Helpers;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;

final class Credential
{
    public function __construct(private string $title = 'E-mail', private string $field = 'email')
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public static function suggestEmail(User $model, string $name)
    {
        $email_test = $suggested_email = mb_strtolower($name);

        $attempts = 1;

        do {
            $email_existing = $model::where('email', $email_test.'@mail.com')->first();

            if ($email_existing) {
                $email_test = $suggested_email.$attempts;

                $attempts++;

                continue;
            }

            $suggested_email = $email_test;
        } while ($email_existing);

        return $suggested_email.'@mail.com';
    }

    public static function generatePassword()
    {
        if (app()->environment('local')) {
            return '12345678';
        }

        return Str::password();
    }
}
