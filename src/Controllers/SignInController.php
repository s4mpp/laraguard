<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{Validator};
use S4mpp\Laraguard\Requests\SignInRequest;
use Illuminate\Http\{RedirectResponse, Request};
use S4mpp\Laraguard\Concerns\Auth as LaraguardAuth;

/**
 * @codeCoverageIgnore
 */
final class SignInController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.login', ['panel' => $panel]);
    }

    public function attempt(SignInRequest $request): RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');
        
        $field_username = $panel->getCredential();
        
        $field = $field_username->getField();
        
        try {
            Utils::rateLimiter();
            
            /** @var Model $model */
            $model = $panel->getModel();

            $user = $model->where([$field => $request->get('username')])->first();

            throw_if(! $user, 'Conta não encontrada. Verifique os dados informados'); 

            /** @var string $password */
            $password = $request->get('password');

            throw_if(! LaraguardAuth::tryLogin($panel, $user, $password), 'Credenciais inválidas. Tente novamente'); 

            throw_if(! Auth::guard($panel->getGuardName())->check(), 'Falha ao realizar o login'); 

            return to_route($panel->getRouteName($panel->getStartModule()->getSlug(), 'index'));
        } catch (\Exception $e) {
            return to_route($panel->getRouteName('login'))
                ->withErrors($e->getMessage())
                ->withInput(['username' => $request->get('username')]);
        }
    }
}
