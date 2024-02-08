<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Http\Request;
use S4mpp\Laraguard\Laraguard;
use S4mpp\MyAccount\MyAccount;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use S4mpp\Laraguard\Facades\RoutesGuard;
use S4mpp\Laraguard\Controllers\BaseController;
use S4mpp\Laraguard\Controllers\LaraguardController;

final class SignUpController extends BaseController
{
	public function index(Request $request): \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory
	{
        return view('laraguard::auth.register', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
	}

	public function save(Request $request): RedirectResponse
	{
		$model = $request->get('laraguard_panel')->getModel();

		if(!$model)
		{
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

	public function finish(Request $request): \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory
	{
        return view('laraguard::auth.register-finished', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
	}
}