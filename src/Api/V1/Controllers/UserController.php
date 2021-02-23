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
     * @SWG\Get(
     *      path="/api/users/{id}",
     *      summary="Get one user by id",
     *      tags={"users"},
     *      description="Get one user by id
    <br> **permission:** _view-users_",
     *      produces={"application/json"},
     *      security={
     *         {
     *             "jwt": {"agency-admin"}
     *         }
     *      },
     *      @SWG\Response(
     *          response=200,
     *          description="200 OK",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/UserTransformerV1")
     *              )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="400 Bad Request",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="500 Internal Server Error",
     *      )
     * )
     *
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
     * @SWG\Get(
     *      path="/api/users",
     *      summary="Get all users",
     *      tags={"users"},
     *      description="Return all users
    <br> **permission:** _view-users_",
     *      produces={"application/json"},
     *      security={
     *         {
     *             "jwt": {"agency-admin"}
     *         }
     *      },
     *      @SWG\Response(
     *          response=200,
     *          description="200 OK",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/UserTransformerV1")
     *              )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="400 Bad Request",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="500 Internal Server Error",
     *      )
     * )
     *
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
     * @SWG\Post(
     *      path="/api/users",
     *      summary="Create user",
     *      tags={"users"},
     *      description="Create user",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="auth",
     *          in="body",
     *          description="User that should be authorization",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserLoginRequestV1")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="200 OK",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string"
     *              )
     *          )
     *      ),
     *     @SWG\Response(
     *          response=210,
     *          description="210 Two way authorization requested",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="hash",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="401 Unauthorized",
     *          @SWG\Schema(ref="#/definitions/UnauthorizedErrorResponseV1")
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="422 Unprocessable Entity",
     *          @SWG\Schema(ref="#/definitions/ValidationErrorResponseV1")
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="500 Server Error",
     *      )
     * )
     *
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
     * @SWG\Put(
     *      path="/api/users",
     *      summary="Update user",
     *      tags={"users"},
     *      description="Update user",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="auth",
     *          in="body",
     *          description="User that should be authorization",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserLoginRequestV1")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="200 OK",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string"
     *              )
     *          )
     *      ),
     *     @SWG\Response(
     *          response=210,
     *          description="210 Two way authorization requested",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="hash",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="401 Unauthorized",
     *          @SWG\Schema(ref="#/definitions/UnauthorizedErrorResponseV1")
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="422 Unprocessable Entity",
     *          @SWG\Schema(ref="#/definitions/ValidationErrorResponseV1")
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="500 Server Error",
     *      )
     * )
     *
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
