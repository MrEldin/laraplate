<?php

namespace Laraplate\Api\V1\Requests\User;

use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Laraplate\Entities\User\Models\User;


/**
 * Class UserCreateRequest
 * @package Tempest\Api\V1\Requests\User
 */
class UserCreateRequest extends FormRequest
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
            User::FIRST_NAME => 'required',
            User::LAST_NAME  => 'required',
            User::EMAIL      => 'required',
            User::PASSWORD   => 'required'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return mixed
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new StoreResourceFailedException('error', $validator->errors());
    }
}
