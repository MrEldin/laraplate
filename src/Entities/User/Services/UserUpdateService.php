<?php

namespace Laraplate\Entities\User\Services;

use Laraplate\Entities\User\Contracts\UserRepository;
use Laraplate\Entities\User\Models\User;

class UserUpdateService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * SurveyQuestionUserCreateService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository

    )
    {
        $this->userRepository = $userRepository;
    }


    public function handle($data, $id)
    {
        return $this->userRepository->update($data, $id);
    }
}
