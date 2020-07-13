<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use BenSampo\Enum\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
            'name' => 'required|string|alpha_num',
            'gender' => ['required', 'gender'],
            'password' => 'required|string|confirmed|min:6|regex:/^(?=.*?[[:alpha:]])(?=.*?[0-9]).{6,}$/iu'
        ];
    }

    public function attributes()
    {
        return __('user');
    }
}
