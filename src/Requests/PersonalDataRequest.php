<?php

namespace S4mpp\Laraguard\Requests;

use Illuminate\Validation\Rule;
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
        $panel = $this->get('laraguard_panel');

        $model = $this->get('laraguard_panel')->getModel();

        return [
            'current_password' => ['required', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique($model->getTable())->ignore(Auth::guard($panel->getGuardName())->id())],
        ];
    }
}
