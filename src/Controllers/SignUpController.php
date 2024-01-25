<?php

namespace S4mpp\Laraguard\Controllers;

use Illuminate\Http\Request;
use S4mpp\MyAccount\MyAccount;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use S4mpp\Laraguard\Facades\RoutesGuard;
use S4mpp\Laraguard\Controllers\LaraguardController;

final class SignUpController extends Controller
{
	public function index()
	{
		$panel = MyAccount::getCurrentPanel();

		$title = 'Cadastro';

		$login_url = route(RoutesGuard::identifier('auth.'.$panel->getSlug())->logout());

		$register_post_url = route('my-account.'.$panel->getSlug().'.register.save');

		return view('my-account::auth.register', compact('panel', 'title', 'login_url', 'register_post_url'));
	}

	public function save(Request $request)
	{
		$panel = MyAccount::getCurrentPanel();
		
		$model = app($panel->getModel());
		
		$validated_input = $request->validate([
			'name' => ['required', 'string'],
			'email' => ['required', 'string', 'email', 'unique:'.$model->getTable()],
			'password' => ['required', 'string', 'min:6'],
		]);

		$new_account = new $model();

		$new_account->name = $validated_input['name'];
		$new_account->email = $validated_input['email'];
		$new_account->password = Hash::make($validated_input['password']);

		$new_account->save();

		return redirect()->back()->withMessage('Cadastro realizado com sucesso!');
	}
}