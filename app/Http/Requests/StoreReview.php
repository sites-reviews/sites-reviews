<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReview extends FormRequest
{
    protected $errorBag = 'store_review';

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
            'advantages' => 'string|nullable',
            'disadvantages' => 'string|nullable',
            'comment' => 'string|nullable',
            'rate' => 'integer|required',
        ];
    }

    public function attributes()
    {
        return __('review');
    }
}
