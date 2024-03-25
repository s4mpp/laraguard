<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Helpers\Utils;
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
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        $panel_title = $panel->getTitle();


        return view('laraguard::auth.password-recovery', compact('panel'));
    }

    public function sendLink(PasswordRecoverySolicitationRequest $request): RedirectResponse
    {
        
        try {
            Utils::rateLimiter();
            
            /** @var Panel $panel */
            $panel = $request->get('laraguard_panel');

            $user = Auth::guard($panel->getGuardName())
                ->getProvider()
                ->retrieveByCredentials(['email' => $request->email ?? null]);

            throw_if(! $user, 'Conta não encontrada'); 

            $status = ConcernsPassword::sendLinkReset($panel, $user);

            throw_if(! is_string($status), 'Falha ao enviar o e-mail'); 

            /** @var string $status */
            return back()->with('message', __($status));
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}
