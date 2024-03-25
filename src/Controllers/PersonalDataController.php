<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Laraguard;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\User;
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
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        $route_save_personal_data = $panel->getRouteName('minha-conta', 'save-personal-data');

        $route_change_password = $panel->getRouteName('minha-conta', 'change-password');

        return Laraguard::layout('laraguard::my-account', [
            'guard' => $panel->getGuardName(),
            'url_save_personal_data' => route($route_save_personal_data),
            'url_save_password' => route($route_change_password),
        ]);
    }

    public function savePersonalData(PersonalDataRequest $request): RedirectResponse
    {
        Utils::rateLimiter();

        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        try {
            /** @var User $user */
            $user = Auth::guard($panel->getGuardName())->user();

            /** @var string $current_password */
            $current_password = $request->get('current_password');
            
            /** @var string $user_password */
            $user_password = $user->password;

            throw_if(! Hash::check($current_password, $user_password), 'Senha inválida. Tente novamente'); 

            $user->name = $request->get('name');
        
            $user->email = $request->get('email');

            $user->save();

            return back()->with('message-personal-data-saved', 'Personal data saved');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage(), 'error-personal-data');
        }
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');
        
        try {
            Utils::rateLimiter();
            
            /** @var User $user */
            $user = Auth::guard($panel->getGuardName())->user();

            /** @var string $new_password */
            $new_password = $request->get('password');

            /** @var string $password_informed */
            $password_informed = $request->get('current_password');

            /** @var string $current_user_password */
            $current_user_password = $user->password;
            
            throw_if(! Hash::check($password_informed, $current_user_password), 'Senha inválida. Tente novamente'); 

            $user->password = Hash::make($new_password);

            $user->save();

            return back()->with('message-password-changed', 'Password has been changed');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage(), 'error-password');
        }
    }
}
