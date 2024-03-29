<?php

namespace S4mpp\Laraguard\Controllers;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Routing\Controller;
use S4mpp\Laraguard\Helpers\Utils;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use S4mpp\Laraguard\Requests\SignUpRequest;
use Illuminate\Http\{RedirectResponse, Request};

/**
 * @codeCoverageIgnore
 */
final class SignUpController extends Controller
{
    public function index(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.register', ['panel' => $panel, 'panel_title' => $panel->getTitle(), 'page_title' => 'Cadastro']);
    }

    public function save(SignUpRequest $request): RedirectResponse
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        $model = $panel->getModel();
        
        throw_if(!$model, 'Invalid model');
        
        /** @var Model $model */
        $new_account = new $model();

        $new_account->name = $request->get('name');
        $new_account->email = $request->get('email');
        
        /** @var string $password */
        $password = $request->get('password');

        $new_account->password = Hash::make($password);

        $new_account->save();

        return to_route($panel->getRouteName('user_registered'));
    }

    public function finish(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        /** @var Panel $panel */
        $panel = $request->get('laraguard_panel');

        return view('laraguard::auth.register-finished', ['panel' => $panel, 'panel_title' => $panel->getTitle(), 'page_title' => 'Cadastro']);
    }
}
