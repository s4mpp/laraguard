<?php

namespace S4mpp\Laraguard\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @codeCoverageIgnore
 */
final class ChangePasswordRequest extends FormRequest
{
    protected $errorBag = 'error-password';

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
            'current_password' => ['required', 'string'],
            'password' => ['required', 'min:6', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'string'],
        ];
    }
}
