<?php

namespace S4mpp\Laraguard\Helpers;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;

final class Credential
{
    public function __construct(private string $title = 'E-mail', private string $field = 'email', private string $type = 'email')
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public static function suggestEmail(User $model, string $name): string
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

    public static function generatePassword(): string
    {
        if (Config::get('app.env') != 'production') {
            return '12345678';
        }

        return Str::password(12, symbols: false);
    }
}
