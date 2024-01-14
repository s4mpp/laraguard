<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Support\Str;
use S4mpp\Laraguard\Laraguard;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->guard = Laraguard::getCurrentGuard();
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
            $field => Str::lower($field_username['title']),
        ])->validate();

        try
        {
            $username = $validated_input[$this->guard->getFieldUsername('field')] ?? null;

            $try_login = $this->tryLogin($this->guard, $username, $validated_input['password']);

            throw_if(!$try_login, 'Invalid credentials. Please try again.');
                
            $redirect_to_inside = $this->redirectToInside($this->guard);

            throw_if(!$redirect_to_inside, 'Failed to login');

            return $redirect_to_inside;
        }
        catch(\Exception $e)
        {
            return to_route($this->guard->getRouteName('signin'))
                ->withErrors($e->getMessage())
                ->withInput([$this->guard->getFieldUsername('field') => $username]);
        }
    }

    public function signout()
    {
        Auth::guard($this->guard->getGuardName())->logout();

        return to_route($this->guard->getRouteName('login'))->withMessage('Logout Successful');
    }
}
