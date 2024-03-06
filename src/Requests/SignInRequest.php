<?php

namespace S4mpp\Laraguard\Requests;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @codeCoverageIgnore
 */
final class SignInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<array{string}>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string>
     */
    public function attributes(): array
    {
        /** @var Panel $panel */
        $panel = $this->get('laraguard_panel');
        
        $credential = $panel->getCredential();

        /** @var string $password_rules */
        $password_rules = __('laraguard::login.password');

        return [
            'username' => $credential->getTitle(),
            'password' => $password_rules,
        ];
    }
}
