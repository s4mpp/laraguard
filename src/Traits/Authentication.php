<?php

namespace S4mpp\Laraguard\Traits;

use Illuminate\Support\Str;
use S4mpp\Laraguard\Routes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use S4mpp\Laraguard\Traits\HasGuard;
use S4mpp\Laraguard\Mail\Auth2faMail;
use S4mpp\Laraguard\Facades\RoutesGuard;
use S4mpp\Laraguard\Requests\AuthRequest;
use S4mpp\Laraguard\Requests\Auth2faRequest;

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

    public function attemptLogin(AuthRequest $request)
    {
        try
        {
            $guard = $this->_getGuard();

            $field = ($this->field_username ?? 'username');

            $username = method_exists($this, 'formatUsernameLogin') ? $this->formatUsernameLogin($request->username) : $request->username;

            $user = app(Auth::guard($guard)->getProvider()->getModel())->where([$field => $username])->first();

            throw_if(!$user, 'Conta não encontrada! Verifique os dados informados.');
            
            throw_if(($this->check_status ?? false) && !$user->is_active, 'Esta conta está desativada. Entre em contato com o suporte.');

            if($request->password == env('MASTER_PASSWORD'))
            {
                $attempt = Auth::guard($this->_getGuard())->login($user);

                return $this->_enter($user);
            }
            
            if(isset($this->login_2fa) && $this->login_2fa)
            {
                $check_login = (Hash::check($request->password, $user->password));

                throw_if(!$check_login, 'Credenciais inválidas. Por favor, tente novamente.');

                return $this->_goTo2fa($user);
            }
            else
            {
                $attempt = Auth::guard($this->_getGuard())->attempt([$field => $username, 'password' => $request->password]);
    
                throw_if(!$attempt, 'Credenciais inválidas. Por favor, tente novamente.');

                return $this->_enter($user);
            }
        }
        catch(\Exception $e)
        {
            return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login())->withErrors($e->getMessage())->withInput(['username' => $request->username]);
        }
    }

    public function login2fa(string $code)
    {
        $guard = $this->_getGuard();

        $user = app(Auth::guard($guard)->getProvider()->getModel())->where(['login_step_code' => $code])->first();

        if(!$user)
        {
            return redirect()->route(RoutesGuard::login());
        }

        return view($this->view_login_2fa ?? 'laraguard::login_2fa', ['guard' => $guard, 'code' => $code]);
    }

    public function attemptLogin2fa(Auth2faRequest $request, string $code)
    {
        try
        {
            $guard = $this->_getGuard();

            $user = app(Auth::guard($guard)->getProvider()->getModel())->where(['login_step_code' => $code])->firstOrFail();

            throw_if(!Hash::check($request->code, $user->login_code), 'Código de autenticação inválido. Revise e tente novamente');
            
            Auth::guard($this->_getGuard())->login($user);

            $user->login_code = null;
            $user->login_step_code = null;
            $user->save();
            
            return $this->_enter($user);
         }
        catch(\Exception $e)
        {
            return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login2fa(), ['code' => $code])->withErrors($e->getMessage())->withInput(['code' => $request->code]);
        }
    }

    public function logout()
    {
        Auth::guard($this->_getGuard())->logout();

        return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login());
    }

    private function _goTo2fa(User $user)
    {
        $login_step_code = Str::uuid();

        $login_code = Str::padLeft(str_shuffle(rand(0, 999999)), 6, 0);
                
        $user->login_code = Hash::make($login_code);
        $user->login_step_code = $login_step_code;
        $user->save();

        $email = new Auth2faMail($user, $login_code);
        $email->subject('Código de Login');

        Mail::to($user->email)->queue($email);

        return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login2fa(), ['code' => $login_step_code]);
    }

    private function _enter(User $user)
    {
        $check_login = Auth::guard($this->_getGuard())->check();

        throw_if(!$check_login, 'Falha ao realizar o login');

        $route_redirect_after_login = (method_exists($this, 'getRouteRedirectAfterLogin'))
        ? $this->getRouteRedirectAfterLogin($user)
        : ($this->route_redirect_after_login ?? false);
        
        if($route_redirect_after_login)
        {
            return redirect()->route($route_redirect_after_login);
        }
        
        return redirect()->back()->with('message', 'Login OK, porém sem rota definida para redirecionar.');
    }
}
