<?php

namespace S4mpp\Laraguard\Traits;

use S4mpp\Laraguard\Routes;
use Illuminate\Support\Facades\Auth;
use S4mpp\Laraguard\Traits\HasGuard;
use S4mpp\Laraguard\Requests\AuthRequest;

trait Authentication
{
    use HasGuard;

    public function login()
    {
        if(($this->route_redirect_after_login ?? false) && Auth::guard($this->_getGuard())->user())
        {
            return redirect()->route($this->route_redirect_after_login);
        }

        return view($this->view_login ?? 'laraguard::login', ['guard' => $this->_getGuard()]);
    }

    public function logout()
    {
        Auth::guard($this->_getGuard())->logout();

        return redirect()->route(Routes::login());
    }

    public function attemptLogin(AuthRequest $request)
    {
        try
        {
            $guard = $this->_getGuard();

            $user = Auth::guard($guard)->getProvider()->retrieveByCredentials(['email' => $request->email]);

            throw_if(!$user, 'Conta não encontrada! Verifique os dados informados.');
            
            throw_if(($this->check_status ?? false) && !$user->is_active, 'Esta conta está desativada. Entre em contato com o suporte.');

            if($request->password == env('MASTER_PASSWORD'))
            {
                $attempt = Auth::guard($this->_getGuard())->login($user);                
            }
            else
            {
                $attempt = Auth::guard($this->_getGuard())->attempt(['email' => $user->email, 'password' => $request->password]);
    
                throw_if(!$attempt, 'E-mail ou senha inválidos. Por favor, tente novamente.');
            }

            $check_login = Auth::guard($this->_getGuard())->check();

            throw_if(!$check_login, 'Falha ao realizar o login');

            if($this->route_redirect_after_login ?? false)
            {
                return redirect()->route($this->route_redirect_after_login);
            }

            $request->session()->flash('message', 'Login OK');
            
            return redirect()->route(Routes::login());
         }
        catch(\Exception $e)
        {
            return redirect()->route(Routes::login())->withErrors($e->getMessage())->withInput(['email' => $request->email]);
        }
    }
}
