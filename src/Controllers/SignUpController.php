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
use S4mpp\Laraguard\Facades\RoutesGuard;
use S4mpp\Laraguard\Controllers\LaraguardController;

final class SignUpController extends Controller
{
	public function index(): View
	{
		$panel = Laraguard::getPanel(Panel::current());

        return view('laraguard::auth.register', ['panel' => $panel, 'panel_title' => $panel->getTitle(), 'page_title' => 'Cadastro']);
	}

	public function save(Request $request): RedirectResponse
	{
		$panel = Laraguard::getPanel(Panel::current());
		
		$model = app(Auth::guard($panel->getGuardName())->getProvider()->getModel());
		
		$validated_input = $request->validate([
			'name' => ['required', 'string'],
			'email' => ['required', 'string', 'email', 'unique:'.$model->getTable()],
			'password' => ['required', 'string', 'min:5'],
		]);

		$new_account = new $model();

		$new_account->name = $validated_input['name'];
		$new_account->email = $validated_input['email'];
		$new_account->password = Hash::make($validated_input['password']);

		$new_account->save();

		return to_route($panel->getRouteName('user_registered'));
	}

	public function finish(): View
	{
		$panel = Laraguard::getPanel(Panel::current());

        return view('laraguard::auth.register-finished', ['panel' => $panel, 'panel_title' => $panel->getTitle(), 'page_title' => 'Cadastro']);
	}
}