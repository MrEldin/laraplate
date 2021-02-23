<?php

namespace Laraplate\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laraplate\Api\V1\Requests\Role\RoleCreateRequest;
use Laraplate\Api\V1\Transformers\RoleTransformer;
use Laraplate\Entities\Role\Contracts\RoleRepository;
use Swagger\Annotations as SWG;

class RoleController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/api/roles/{id}",
     *      summary="Get one role by id",
     *      tags={"roles"},
     *      description="Get one role by id
    <br> **permission:** _view-role_",
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
     *                  @SWG\Items(ref="#/definitions/RoleTransformerV1")
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
     * Return all roles
     * @param $id
     * @param RoleRepository $roleRepository
     * @return \Dingo\Api\Http\Response
     */
    public function show($id, RoleRepository $roleRepository)
    {
        return $this->response
            ->item($roleRepository->find($id), new RoleTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *      path="/api/roles",
     *      summary="Get all roles",
     *      tags={"roles"},
     *      description="Return all roles
    <br> **permission:** _view-role_",
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
     *                  @SWG\Items(ref="#/definitions/RoleTransformerV1")
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
     * Return all roles
     * @param RoleRepository $roleRepository
     * @return \Dingo\Api\Http\Response
     */
    public function index(RoleRepository $roleRepository)
    {
        return $this->response
            ->collection($roleRepository->all(), new RoleTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *      path="/api/roles",
     *      summary="Create role",
     *      tags={"roles"},
     *      description="Create role",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="auth",
     *          in="body",
     *          description="User that should be authorization",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RoleCreateRequestV1")
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
     * @param RoleCreateRequest $roleCreateRequest
     * @param RoleRepository $roleRepository
     * @return \Dingo\Api\Http\Response
     */
    public function create(RoleCreateRequest $roleCreateRequest, RoleRepository $roleRepository)
    {
        $role = $roleRepository->create([
            'name' => str_replace(strtolower($roleCreateRequest->name), ' ', '-'),
            'label' => $roleCreateRequest->name
        ]);

        return $this->response
            ->item($role, new RoleTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Put(
     *      path="/api/roles",
     *      summary="Update role",
     *      tags={"roles"},
     *      description="Update role",
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
     * @param RoleRepository $roleRepository
     * @return void
     */
    public function update(Request $request, $id, RoleRepository $roleRepository)
    {
        $roleRepository->update($request->all(), $id);
    }

    /**
     * @SWG\Delete(
     *      path="/api/roles/{id}",
     *      summary="Delete role",
     *      tags={"roles"},
     *      description="Update role",
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
     * @param RoleRepository $roleRepository
     * @return void
     */
    public function destroy($id, RoleRepository $roleRepository)
    {
        $roleRepository->delete($id);
    }

    /**
     * @SWG\Post(
     *      path="/api/roles/{roleId}/permissions/{permissionId}",
     *      summary="Attach permission to role",
     *      tags={"roles"},
     *      description="Update role",
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
     * @param $roleId
     * @param $permissionId
     * @param RoleRepository $roleRepository
     * @return \Dingo\Api\Http\Response
     */
    public function attachPermission(Request $request, $roleId, $permissionId , RoleRepository $roleRepository)
    {
        $role = $roleRepository->find($roleId);
        $role->permissions()->attach($permissionId);

        return $this->response->item($role, new RoleTransformer())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Delete(
     *      path="/api/roles/{roleId}/permissions/{permissionId}",
     *      summary="Detach permission from role",
     *      tags={"roles"},
     *      description="Update role",
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
     * @param $roleId
     * @param $permissionId
     * @param RoleRepository $roleRepository
     * @return \Dingo\Api\Http\Response
     */
    public function detachPermission(Request $request, $roleId, $permissionId , RoleRepository $roleRepository)
    {
        $role = $roleRepository->find($roleId);
        $role->permissions()->detach($permissionId);

        return $this->response->item($role, new RoleTransformer())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Put(
     *      path="/api/roles/{roleId}/permissions/{permissionId}",
     *      summary="Attach permission to role",
     *      tags={"roles"},
     *      description="Update role",
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
     * @param $roleId
     * @param $permissionId
     * @param RoleRepository $roleRepository
     * @return \Dingo\Api\Http\Response
     */
    public function syncPermission(Request $request, $roleId, $permissionId , RoleRepository $roleRepository)
    {
        $role = $roleRepository->find($roleId);

        $permission = $role->permissions()->where('permission_id', $permissionId)->first();

        if($permission){
            $role->permissions()->detach($permissionId);

            return $this->response->item($role, new RoleTransformer())->setStatusCode(Response::HTTP_OK);
        }

        $role->permissions()->attach($permissionId);

        return $this->response->item($role, new RoleTransformer())->setStatusCode(Response::HTTP_OK);
    }
}
