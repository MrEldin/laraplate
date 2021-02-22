<?php

namespace SmartlyJobs\Api\V1\Requests\Permission;

use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use SmartlyJobs\Entities\Permission\Models\Permission;

/**
 * @SWG\Definition (
 *      definition="PermissionUpdateRequestV1",
 *      required={"name", "label", "email"},
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string",
 *          example="John"
 *      ),
 *      @SWG\Property(
 *          property="label",
 *          description="label",
 *          type="string",
 *          example="Some label"
 *      )
 * )
 *
 * Class PermissionCreateRequest
 * @package Tempest\Api\V1\Requests\User
 */
class PermissionUpdateRequest extends FormRequest
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
