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

        $route_save_personal_data = $panel->getRouteName('meus-dados', 'save-personal-data');

        return Laraguard::layout('laraguard::personal-data', [
            'guard' => $panel->getGuardName(),
            'url_save_personal_data' => route($route_save_personal_data),
        ]);
    }

    public function save(PersonalDataRequest $request): RedirectResponse
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
}
