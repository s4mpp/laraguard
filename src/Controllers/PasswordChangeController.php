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
final class PasswordChangeController extends Controller
{
    public function __invoke(Request $request): null|View|\Illuminate\Contracts\View\Factory
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        $route_change_password = $panel->getRouteName('alterar-senha', 'save-password');

        return Laraguard::layout('laraguard::change-password', [
            'guard' => $panel->getGuardName(),
            'url_save_password' => route($route_change_password),
        ]);
    }

    public function save(ChangePasswordRequest $request): RedirectResponse
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
