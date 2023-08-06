<?php

namespace S4mpp\Laraguard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecoveryPasswordChangeRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:5'],
        ];
    }

    // public function withValidator($validator)
    // {
    //     if($validator->fails())
    //     {
    //         return false;
    //     }

    //     $model = $this->route()->getController()->getModelAuthProvider();
        
    //     $this->merge([
    //         'user' => $model::whereTokenPasswordRecovery($this->token_password_recovery)->firstOrFail(),
    //     ]); 
        
    //     $validator->after(function($validator) 
    //     {
    //         if(!$this->user)
    //         {
    //             $validator->errors()->add('invalid_code', 'Código de alteração inválido.');
    //         }
    //     });
    // }
}
