<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Utils;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class SignInController extends BaseController
{
    public function index(Request $request): \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory
    {
        return view('laraguard::auth.login', ['panel' => $request->get('laraguard_panel')]);
    }
    
    public function attempt(Request $request): RedirectResponse
    {
        $field_username = $request->get('laraguard_panel')->getFieldUsername();

        $field = $field_username->getField();

        $validated_input = Validator::make($request->only([$field, 'password']), [
            $field => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'password' => __('laraguard::login.password'),
            $field => Str::lower($field_username->getTitle()),
        ])->validate();
        
        try
        {
            $username = $validated_input[$field] ?? null;

            $model = $request->get('laraguard_panel')->getModel();

            if(!$model)
            {
                throw new \Exception('Invalid model');
            }
            
            /** @phpstan-ignore-next-line  */
            $user = $model->where([$field => $username])->first();

            throw_if(!$user, Utils::translate('laraguard::login.account_not_found'));

            throw_if(!$request->get('laraguard_panel')->tryLogin($user, $validated_input['password']), Utils::translate('laraguard::login.invalid_credentials'));

            throw_if(!$request->get('laraguard_panel')->checkIfIsUserIsLogged(), Utils::translate('laraguard::login.login_failed'));

            return to_route($request->get('laraguard_panel')->getRouteName('my-account', 'index'));
        }
        catch(\Exception $e)
        {
            return to_route($request->get('laraguard_panel')->getRouteName('login'))
                ->withErrors($e->getMessage())
                ->withInput([$field => $username]);
        }
    }
}
