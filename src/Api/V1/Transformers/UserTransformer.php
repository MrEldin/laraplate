<?php

namespace SmartlyJobs\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use SmartlyJobs\Entities\User\Models\User;

/**
 * @SWG\Definition (
 *      definition="UserTransformerV1",
 *      required={},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="registeredName",
 *          description="registeredName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address",
 *          description="address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="postalCode",
 *          description="postalCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="town",
 *          description="town",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      )
 * )
 *
 * Class UserTransformer
 *
 * @package Tempest\Api\V1\Transformers
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
//    protected $availableIncludes = ['user'];

    /**
     * Transform user data
     *
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            User::ID         => (int)$user->{User::ID},
            User::FIRST_NAME => $user->{User::FIRST_NAME},
            User::LAST_NAME  => $user->{User::LAST_NAME},
            User::EMAIL      => $user->{User::EMAIL}
        ];
    }


//    protected function includeRole(User $user)
//    {
//        return $this->item($user->user, new UserTransformer(), false);
//    }
}
