<?php
namespace S4mpp\MyAccount\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

trait ProtectedByPassword
{
    public ?string $password = null;

    private function _validatePassword()
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        $customer = Auth::user();

		throw_if(RateLimiter::tooManyAttempts('password:'.$customer->id, 3), 'Você excedeu a quantidade de tentativas por tempo. Aguarde alguns segundos e tente novamente.');

		RateLimiter::hit('password:'.$customer->id);
        
		throw_if(!Hash::check($this->password, $customer->password), 'Senha inválida. Tente novamente');
    }
}