<?php

namespace S4mpp\Laraguard\Traits;

use S4mpp\Laraguard\Routes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use S4mpp\Laraguard\Traits\HasGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use S4mpp\Laraguard\Mail\PasswordRecoveryMail;
use S4mpp\Laraguard\Requests\RecoveryPasswordChangeRequest;
use S4mpp\Laraguard\Requests\RecoveryPasswordSolicitationRequest;

trait RecoveryPassword
{
    use HasGuard;

    public function forgotPassword()
    {
        $guard = $this->_getGuard();

        return view($this->view_forgot_password ?? 'laraguard::forgot_password', compact('guard'));
    }

    public function recoveryPassword(RecoveryPasswordSolicitationRequest $request)
    {
        $guard = $this->_getGuard();

        $user = Auth::guard($guard)->getProvider()->retrieveByCredentials(['email' => $request->email]);
        
        if(!$user)
        {
            return redirect()->back()->withErrors('E-mail/conta não encontrada.');
        }

        $user->token_password_recovery = md5(rand());

        $user->save();

        $this->_sendEmailRecoveryPasswordLink($user);

        $request->session()->flash('message', 'E-mail de recuperação de senha enviado para '.$user->email.'. Acesse sua caixa de entrada e clique no link recebido para redefinir a senha.');

        return redirect()->back();
    }

    public function changePasswordRecovery(string $token_password_recovery)
    {
        $guard = $this->_getGuard();

        $user = Auth::guard($guard)->getProvider()->getModel()::where('token_password_recovery', $token_password_recovery)->first();

        if(!$user)
        {
            return redirect()->route(Routes::identifier($this->route_identifier ?? null)->forgotPassword())->withErrors('Código de recuperação de senha inválido ou expirado. Tente solicitar o código novamente.');
        }

        return view($this->view_change_password ?? 'laraguard::change_password', compact('user', 'token_password_recovery'));
    }

    public function storePasswordRecovery(RecoveryPasswordChangeRequest $request, string $token_password_recovery)
    {
        $guard = $this->_getGuard();

        $user = Auth::guard($guard)->getProvider()->getModel()::where('token_password_recovery', $token_password_recovery)->firstOrFail();

        $user->token_password_recovery = null;
        $user->password = Hash::make($request->password);
        
        $user->save();

        $request->session()->flash('message', 'Senha alterada com sucesso! Você já pode acessar a conta com sua nova senha.');

        return redirect()->route(Routes::identifier($this->route_identifier ?? null)->login())->withInput(['email' => $user->email]);
    }

    private function _sendEmailRecoveryPasswordLink(Authenticatable $user)
    {
        $link = route(Routes::identifier($this->route_identifier ?? null)->changePasswordRecovery(), ['token_password_recovery' => $user->token_password_recovery]);

        $email = new PasswordRecoveryMail($user, $link);
        $email->subject('Recuperação de senha');

        Mail::to($user->email)->queue($email);
    }
}
