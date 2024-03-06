<?php

namespace S4mpp\Laraguard\Requests;

use Illuminate\Validation\Rule;
use S4mpp\Laraguard\Base\Panel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @codeCoverageIgnore
 */
final class PersonalDataRequest extends FormRequest
{
    protected $errorBag = 'error-personal-data';

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

        $table = $model?->getTable() ?? '';

        return [
            'current_password' => ['required', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique($table)->ignore(Auth::guard($panel->getGuardName())->id())],
        ];
    }
}
