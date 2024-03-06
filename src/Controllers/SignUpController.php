<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Requests\SignUpRequest;
use Illuminate\Http\{RedirectResponse, Request};

/**
 * @codeCoverageIgnore
 */
final class SignUpController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        return view('laraguard::auth.register', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
    }

    public function save(SignUpRequest $request): RedirectResponse
    {
        $model = $request->get('laraguard_panel')->getModel();

        throw_if(!$model, 'Invalid model');

        $new_account = new $model();

        $new_account->name = $request->get('name');
        $new_account->email = $request->get('email');
        $new_account->password = Hash::make($request->get('password'));

        $new_account->save();

        return to_route($request->get('laraguard_panel')->getRouteName('user_registered'));
    }

    public function finish(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        return view('laraguard::auth.register-finished', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
    }
}
