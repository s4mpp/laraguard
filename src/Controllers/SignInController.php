<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Panel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SignInController extends Controller
{
    public function index()
    { 
        $panel = Laraguard::currentPanel();

        return view('laraguard::auth.login', ['panel' => $panel, 'panel_title' => $panel->getTitle(), 'page_title' => __('laraguard::login.title')]);
    }
    
    public function attempt()
    {
        $panel = Laraguard::currentPanel();

        $field_username = $panel->getFieldUsername();

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
            $username = $validated_input[$panel->getFieldUsername('field')] ?? null;

            $model = Auth::guard($panel->getGuardName())->getProvider()->getModel();

            $user = app($model)->where([$panel->getFieldUsername('field') => $username])->first();

            throw_if(!$user, __('laraguard::login.account_not_found'));

            throw_if(!$panel->tryLogin($user, $validated_input['password']), __('laraguard::login.invalid_credentials'));

            throw_if(!$panel->checkIfIsUserIsLogged(), __('laraguard::login.login_failed'));
                
            return to_route($panel->getRouteName('my-account', 'index'));
        }
        catch(\Exception $e)
        {
            return to_route($panel->getRouteName('login'))
                ->withErrors($e->getMessage())
                ->withInput([$panel->getFieldUsername('field') => $username]);
        }
    }
}
