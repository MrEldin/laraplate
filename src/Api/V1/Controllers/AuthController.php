<?php

namespace Laraplate\Api\V1\Controllers;

use Laraplate\Api\V1\Requests\User\UserLoginRequest;
use Laraplate\Entities\User\Services\UserLoginService;

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
