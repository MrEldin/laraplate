<?php

namespace Laraplate\Api\V1\Requests\User;


use Illuminate\Foundation\Http\FormRequest;
/**
 * @SWG\Definition (
 *      definition="UserLoginRequestV1",
 *      required={"email", "password"},
 *      @SWG\Property(
 *          property="email",
 *          description="email address",
 *          type="string",
 *          example="admin@chord.agency"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="password",
 *          type="string",
 *          example="password"
 *      ),
 *      @SWG\Property(
 *          property="device_token",
 *          description="device token",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="device_type",
 *          description="device type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="two_way_token",
 *          description="Already saved two way auth token (save this device token)",
 *          type="string"
 *      )
 * )
 *
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
