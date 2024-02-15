<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\{Laraguard, Utils};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Validator};

final class SignInController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.login', ['panel' => $panel]);
    }

    public function attempt(Request $request): RedirectResponse
    {
        $field_username = $request->get('laraguard_panel')->auth()->getCredentialFields();

        $field = $field_username->getField();

        $validated_input = Validator::make($request->only([$field, 'password']), [
            $field => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'password' => __('laraguard::login.password'),
            $field => Str::lower($field_username->getTitle()),
        ])->validate();

        $username = $validated_input[$field] ?? null;

        try {
            $model = $request->get('laraguard_panel')->getModel();

            if (! $model) {
                throw new \Exception('Invalid model');
            }

            $user = $model->where([$field => $username])->first();

            throw_if(! $user, Utils::translate('laraguard::auth.account_not_found'));

            throw_if(! $request->get('laraguard_panel')->auth()->tryLogin($user, $validated_input['password']), Utils::translate('laraguard::auth.invalid_credentials'));

            throw_if(! $request->get('laraguard_panel')->auth()->checkIfIsUserIsLogged(), Utils::translate('laraguard::auth.login_failed'));

            return to_route($request->get('laraguard_panel')->getRouteName('my-account', 'index'));
        } catch (\Exception $e) {
            return to_route($request->get('laraguard_panel')->getRouteName('login'))
                ->withErrors($e->getMessage())
                ->withInput([$field => $username]);
        }
    }
}
