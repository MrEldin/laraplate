<?php

namespace Laraplate\Api\V1\Requests\Permission;

use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laraplate\Entities\Permission\Models\Permission;

/**
 * Class PermissionCreateRequest
 * @package Tempest\Api\V1\Requests\User
 */
class PermissionCreateRequest extends FormRequest
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
            Permission::NAME => 'required|unique:roles',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return mixed
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new StoreResourceFailedException('error', $validator->errors());
    }
}
