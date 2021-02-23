<?php

namespace Laraplate\Entities\User\Services;

use Laraplate\Entities\User\Contracts\UserRepository;
use Laraplate\Entities\User\Models\User;

class UserCreateService
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


    public function handle($data)
    {
        return $this->userRepository->create([
            User::FIRST_NAME => $data[User::FIRST_NAME],
            User::LAST_NAME  => $data[User::LAST_NAME],
            User::EMAIL      => $data[User::EMAIL],
            User::PASSWORD   => isset($data[User::PASSWORD]) ? $data[User::PASSWORD] : str_random(17),
        ]);
    }
}
