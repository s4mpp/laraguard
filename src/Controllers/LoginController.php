<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Guard;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    private Guard $guard;

    public function __construct(Request $request)
    {
        $guard = Laraguard::getCurrentGuard($request->route()->getAction('as'));
        
        if(!$guard)
        {
            abort(404);
        }
        
        $this->guard = $guard;
    }

    public function index()
    { 
        return view('laraguard::login', ['guard' => $this->guard]);
    }
    
    public function attempt()
    {
        $field_username = $this->guard->getFieldUsername();

        $field = $field_username['field'];

        $validated_input = Validator::make(request()->only([$field, 'password']), [
            $field => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'password' => __('laraguard::login.password'),
            $field => Str::lower($field_username['title']),
        ])->validate();

        try
        {
            $username = $validated_input[$this->guard->getFieldUsername('field')] ?? null;

            $model = Auth::guard($this->guard->getGuardName())->getProvider()->getModel();

            $user = app($model)->where([$this->guard->getFieldUsername('field') => $username])->first();

            throw_if(!$user, __('laraguard::login.account_not_found'));

            throw_if(!$this->guard->tryLogin($user, $validated_input['password']), __('laraguard::login.invalid_credentials'));

            throw_if(!$this->guard->checkLogin(), __('laraguard::login.login_failed'));
                
            return $this->guard->redirectToInside();
        }
        catch(\Exception $e)
        {
            return to_route($this->guard->getRouteName('login'))
                ->withErrors($e->getMessage())
                ->withInput([$this->guard->getFieldUsername('field') => $username]);
        }
    }

    public function signout()
    {
        if(!$this->guard->logout())
        {
            return redirect()->back();
        }
        
        return to_route($this->guard->getRouteName('login'))->withMessage(__('laraguard::login.logout_successfull'));
    }
}
