<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Utils;
use S4mpp\Laraguard\Laraguard;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Http\{RedirectResponse, Request};
use S4mpp\Laraguard\Requests\PersonalDataRequest;
use S4mpp\Laraguard\Requests\ChangePasswordRequest;

/**
 * @codeCoverageIgnore
 */
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
            'url_save_password' => route($route_change_password),
        ]);
    }

    public function savePersonalData(PersonalDataRequest $request): RedirectResponse
    {
        Utils::rateLimiter();

        $panel = $request->get('laraguard_panel');

        try {
            $user = Auth::guard($panel->getGuardName())->user();

            throw_if(! Hash::check($request->get('current_password'), $user?->password), __('laraguard::auth.invalid_password'));

            if (isset($user->name)) {
                $user->name = $request->get('name');
            }

            if (isset($user->email)) {
                $user->email = $request->get('email');
            }

            $user->save();

            return back()->with('message-personal-data-saved', 'Personal data saved');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage(), 'error-personal-data');
        }
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $panel = $request->get('laraguard_panel');
        
        try {
            Utils::rateLimiter();
            
            $user = Auth::guard($panel->getGuardName())->user();

            throw_if(! Hash::check($request->get('current_password'), $user?->password), __('laraguard::auth.invalid_password'));

            if (isset($user->password)) {
                $user->password = Hash::make($request->get('password'));
            }

            $user->save();

            return back()->with('message-password-changed', 'Password has been changed');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage(), 'error-password');
        }
    }
}
