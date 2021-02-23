<?php

namespace Laraplate\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laraplate\Api\V1\Requests\User\UserCreateRequest;
use Laraplate\Api\V1\Transformers\UserTransformer;
use Laraplate\Entities\User\Contracts\UserRepository;

class UserController extends Controller
{

    /**
     * Return user
     * @param $id
     * @param UserRepository $userRepository
     * @return \Dingo\Api\Http\Response
     */
    public function show($id, UserRepository $userRepository)
    {
        return $this->response
            ->item($userRepository->find($id), new UserTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Return all users
     * @param UserRepository $userRepository
     * @return \Dingo\Api\Http\Response
     */
    public function index(UserRepository $userRepository)
    {
        return $this->response
            ->collection($userRepository->all(), new UserTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param UserCreateRequest $userCreateRequest
     * @param UserRepository $userRepository
     * @return void
     */
    public function create(UserCreateRequest $userCreateRequest, UserRepository $userRepository)
    {
        $userRepository->create($userCreateRequest->all());
    }


    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @param $id
     * @param UserRepository $userRepository
     * @return void
     */
    public function update(Request $request, $id, UserRepository $userRepository)
    {
        $userRepository->update($request->all(), $id);
    }

}
