<?php

namespace Laraplate\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use Laraplate\Entities\User\Models\User;

/**
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
