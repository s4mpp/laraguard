<?php

namespace S4mpp\Laraguard\Controllers;

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
        return view('laraguard::auth.register', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
    }

    public function save(SignUpRequest $request): RedirectResponse
    {
        $model = $request->get('laraguard_panel')->getModel();
        
        throw_if(!$model, 'Invalid model');
        
        /** @var Model $model */
        $new_account = new $model();

        if(isset($new_account->name))
        {
            $new_account->name = $request->get('name');
        }

        if(isset($new_account->email))
        {
            $new_account->email = $request->get('email');
            
        }

        if(isset($new_account->password))
        {

            $new_account->password = Hash::make($request->get('password'));
        }

        $new_account->save();

        return to_route($request->get('laraguard_panel')->getRouteName('user_registered'));
    }

    public function finish(Request $request): View|\Illuminate\Contracts\View\Factory
    {
        return view('laraguard::auth.register-finished', ['panel' => $request->get('laraguard_panel'), 'panel_title' => $request->get('laraguard_panel')->getTitle(), 'page_title' => 'Cadastro']);
    }
}
