<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\{Laraguard, Utils};
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Hash, Password};
use S4mpp\Laraguard\Concerns\Password as ConcernsPassword;
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};
use S4mpp\Laraguard\Requests\{PasswordResetRequest, RecoveryPasswordChangeRequest};

/**
 * @codeCoverageIgnore
 */
final class PasswordResetController extends Controller
{
    public function index(Request $request, string $token): View|\Illuminate\Contracts\View\Factory|RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        $user = Password::broker($panel->getGuardName())->getUser(['email' => $request->get('email')]);

        if (! $user || ! Password::tokenExists($user, $token)) {
            return to_route($panel->getRouteName('recovery_password'))->withErrors('Token inválido'); 
        }


        return view('laraguard::auth.password-reset', compact('panel', 'user', 'token'));
    }

    public function storePassword(PasswordResetRequest $request): RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        $user = Password::broker($panel->getGuardName())->getUser(['email' => $request->get('email')]);

        if (! $user || ! Password::tokenExists($user, $request->token ?? '')) {
            return to_route($panel->getRouteName('recovery_password'))->withErrors('Token inválido'); 
        }

        /** @var string $token */
        $token = $request->get('token');
        
        /** @var string $password */
        $password = $request->get('password');

        $status = ConcernsPassword::reset($panel, $user, $token, $password);

        /** @var string $error */
        $error = __('passwords.user');
        
        return $status === PasswordBroker::PASSWORD_RESET
            ? to_route($panel->getRouteName('login'))->with('message', __($status))->withInput(['email' => $user->email])
            : back()->withErrors($error); 
    }
}
