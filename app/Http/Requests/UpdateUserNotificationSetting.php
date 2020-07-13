<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use BenSampo\Enum\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserNotificationSetting extends FormRequest
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
            'email_response_to_my_review' => 'required|boolean',
            'db_response_to_my_review' => 'required|boolean',
            'db_when_review_was_liked' => 'required|boolean'
        ];
    }

    public function attributes()
    {
        return __('user_notification_setting');
    }
}
