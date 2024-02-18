<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\{RedirectResponse, Request};

final class SignUpController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        return view('laraguard::auth.register', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
    }

    public function save(Request $request): RedirectResponse
    {
        $model = $request->get('laraguard_panel')->getModel();

        if (! $model) {
            throw new \Exception('Invalid model');
        }

        $validated_input = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:'.$model->getTable()],
            'password' => ['required', 'string', 'min:5'],
        ]);

        $new_account = $model;

        $new_account->name = $validated_input['name'];
        $new_account->email = $validated_input['email'];
        $new_account->password = Hash::make($validated_input['password']);

        $new_account->save();

        return to_route($request->get('laraguard_panel')->getRouteName('user_registered'));
    }

    public function finish(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        return view('laraguard::auth.register-finished', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
    }
}
