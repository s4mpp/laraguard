<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Password};
use S4mpp\Laraguard\Concerns\Password as ConcernsPassword;
use S4mpp\Laraguard\Controllers\{BaseController, LaraguardController};
use S4mpp\Laraguard\Notifications\{ResetPassword, ResetPassword as NotificationsResetPassword};
use S4mpp\Laraguard\Requests\{PasswordRecoverySolicitationRequest, RecoveryPasswordSolicitationRequest};

/**
 * @codeCoverageIgnore
 */
final class PasswordRecoveryController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        $panel_title = $request->get('laraguard_panel')->getTitle();

        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.password-recovery', compact('panel'));
    }

    public function sendLink(PasswordRecoverySolicitationRequest $request): RedirectResponse
    {
        
        try {
            Utils::rateLimiter();
            
            $panel = $request->get('laraguard_panel');

            $user = Auth::guard($request->get('laraguard_panel')->getGuardName())
                ->getProvider()
                ->retrieveByCredentials(['email' => $request->email ?? null]);

            throw_if(! $user, __('laraguard::password_recovery.account_not_found')); // @phpstan-ignore-line

            $status = ConcernsPassword::sendLinkReset($panel, $user);

            throw_if(! is_string($status), __('laraguard::password_recovery.fail_to_send_email')); // @phpstan-ignore-line

            return back()->with('message', __($status));
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}
