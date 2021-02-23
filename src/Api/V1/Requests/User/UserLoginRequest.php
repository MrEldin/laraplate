<?php

namespace Laraplate\Api\V1\Requests\User;


use Illuminate\Foundation\Http\FormRequest;
/**
 * Class UserLoginRequest
 * @package Tempest\Api\V1\Requests\User
 */
class UserLoginRequest extends FormRequest
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
            'email'    => 'required|email',
            'password' => 'required'
        ];
    }
}
