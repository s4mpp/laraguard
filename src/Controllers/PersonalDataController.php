<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Validation\Rule;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PersonalDataController extends Controller
{
    public function __invoke()
	{
		$panel = Laraguard::getPanel(Panel::current()); 

		$route_save_personal_data = $panel->getRouteName('my-account', 'save-personal-data');
		
		$route_save_new_password = $panel->getRouteName('my-account', 'save-new-password');

		return Laraguard::layout('laraguard::my-account', [
			'guard' => $panel->getGuardName(),
			'url_save_personal_data' => route($route_save_personal_data),
			'url_save_new_password' => route($route_save_new_password)
		]);
	}

	public function savePersonalData()
	{
		$panel = Laraguard::getPanel(Panel::current()); 

		$model = app(Auth::guard($panel->getGuardName())->getProvider()->getModel());

		$validated_data = request()->validate([
			'current_password' => ['required', 'string'],
			'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique($model->getTable())->ignore(Auth::id())],
		]);

		try
		{
			$account = Auth::user();
			
			$panel->checkPassword($account, $validated_data['current_password']);
	
			$account->name = $validated_data['name'];
			$account->email = $validated_data['email'];
	
			$account->save();
	
			return redirect()->back()->withMessage('Personal data saved');
		}
		catch(\Exception $e)
		{
			return redirect()->back()->withErrors($e->getMessage());
		}
	}

	public function changePassword()
	{
		$validated_data = request()->validate([
			'current_password' => ['required', 'string'],
            'password' => ['required', 'min:6', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'string'],
		]);

		try
		{
			$panel = Laraguard::getPanel(Panel::current()); 
	
			$account = Auth::user();
				
			$panel->checkPassword($account, $validated_data['current_password']);

			$account->password = Hash::make($validated_data['password']);
	
			$account->save();
	
			return redirect()->back()->withMessage('Password has been changed');
		}
		catch(\Exception $e)
		{
			return redirect()->back()->withErrors($e->getMessage());
		}
	}
}
