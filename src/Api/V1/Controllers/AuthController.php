<?php

namespace SmartlyJobs\Api\V1\Controllers;

use SmartlyJobs\Api\V1\Requests\User\UserLoginRequest;
use SmartlyJobs\Entities\User\Services\UserLoginService;

class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @SWG\Post(
     *      path="/api/login",
     *      summary="User authorization",
     *      tags={"auth"},
     *      description="Authorize a user",
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
     * @param UserLoginRequest $request
     * @param UserLoginService $userLoginService
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request, UserLoginService $userLoginService)
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = $userLoginService->handle($credentials)) {
            return response()->json(['error' => 'Check your credentials!'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @SWG\Get(
     *      path="/api/me",
     *      summary="Authorizated user",
     *      tags={"auth"},
     *      description="User that is logged in",
     *      produces={"application/json"},
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
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json(['data' => array_merge(
            auth()->user()->toArray(),
            ['permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray()]
        )]);
    }



    /**
     * @SWG\Post(
     *      path="/api/logout",
     *      summary="Logout user",
     *      tags={"auth"},
     *      description="Deactivate jwt token",
     *      produces={"application/json"},
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
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * @SWG\Post(
     *      path="/api/refresh",
     *      summary="Refresh token",
     *      tags={"auth"},
     *      description="Refresh jwt token",
     *      produces={"application/json"},
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
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
