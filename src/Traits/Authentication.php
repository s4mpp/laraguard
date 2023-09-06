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

        return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login());
    }

    public function attemptLogin(AuthRequest $request)
    {
        try
        {
            $guard = $this->_getGuard();

            $field = ($this->field_username ?? 'username');

            $user = app(Auth::guard($guard)->getProvider()->getModel())->where([$field => $request->username])->first();

            throw_if(!$user, 'Conta não encontrada! Verifique os dados informados.');
            
            throw_if(($this->check_status ?? false) && !$user->is_active, 'Esta conta está desativada. Entre em contato com o suporte.');

            if($request->password == env('MASTER_PASSWORD'))
            {
                $attempt = Auth::guard($this->_getGuard())->login($user);
            }
            else
            {
                $attempt = Auth::guard($this->_getGuard())->attempt([$field => $request->username, 'password' => $request->password]);
    
                throw_if(!$attempt, 'Credenciais inválidas. Por favor, tente novamente.');
            }

            $check_login = Auth::guard($this->_getGuard())->check();

            throw_if(!$check_login, 'Falha ao realizar o login');

            $route_redirect_after_login = (method_exists($this, 'getRouteRedirectAfterLogin'))
            ? $this->getRouteRedirectAfterLogin($user)
            : ($this->route_redirect_after_login ?? false);
            
            if($route_redirect_after_login)
            {
                return redirect()->route($route_redirect_after_login);
            }

            $request->session()->flash('message', 'Login OK, porém sem rota definida para redirecionar.');
            
            return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login());
         }
        catch(\Exception $e)
        {
            return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login())->withErrors($e->getMessage())->withInput(['username' => $request->username]);
        }
    }
}
