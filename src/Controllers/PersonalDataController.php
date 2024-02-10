<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Routing\Controller;

final class PersonalDataController extends Controller
{
    public function __invoke(Request $request): null|View|\Illuminate\Contracts\View\Factory
    {
        $panel = $request->get('laraguard_panel');

        $route_save_personal_data = $panel->getRouteName('my-account', 'save-personal-data');

        $route_change_password = $panel->getRouteName('my-account', 'change-password');

        return Laraguard::layout('laraguard::my-account', [
            'guard' => $panel->getGuardName(),
            'url_save_personal_data' => route($route_save_personal_data),
            'url_save_new_password' => route($route_change_password),
        ]);
    }

    public function savePersonalData(Request $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');

        $model = $panel->getModel();

        if (! $model) {
            throw new \Exception('Invalid model');
        }

        $validated_data = $request->validate([
            'current_password' => ['required', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique($model->getTable())->ignore(Auth::id())],
        ]);

        try {
            $account = Auth::user();

            if (! $account) {
                throw new \Exception('Account not found');
            }

            $panel->checkPassword($account, $validated_data['current_password']);

            $account->name = $validated_data['name'];
            $account->email = $validated_data['email'];

            $account->save();

            return back()->with('message', 'Personal data saved');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');

        $validated_data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'min:6', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'string'],
        ]);

        try {
            $account = Auth::user();

            if (! $account) {
                throw new \Exception('Account not found');
            }

            $panel->checkPassword($account, $validated_data['current_password']);

            $account->password = Hash::make($validated_data['password']);

            $account->save();

            return back()->with('message', 'Password has been changed');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
