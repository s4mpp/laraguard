<?php

namespace S4mpp\Laraguard\Requests;

use S4mpp\Laraguard\Base\Panel;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @codeCoverageIgnore
 */
final class SignUpRequest extends FormRequest
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
        /** @var Panel $panel */
        $panel = $this->get('laraguard_panel');
        
        $model = $panel->getModel();

        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:'.$model?->getTable()],
            'password' => ['required', 'string', 'min:5'],
        ];
    }
}
