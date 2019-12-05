<?php

namespace SmartlyJobs\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SmartlyJobs\Api\V1\Requests\Permission\PermissionCreateRequest;
use SmartlyJobs\Api\V1\Transformers\PermissionTransformer;
use SmartlyJobs\Entities\Permission\Contracts\PermissionRepository;
use SmartlyJobs\Entities\Permission\Models\Permission;
use SmartlyJobs\Entities\User\Models\User;

class PermissionController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/api/permissions/{id}",
     *      summary="Get one permission by id",
     *      tags={"permissions"},
     *      description="Get one permission by id
    <br> **permission:** _view-permission_",
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
     *                  @SWG\Items(ref="#/definitions/PermissionTransformerV1")
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
     * Return all permissions
     * @param $id
     * @param PermissionRepository $permissionRepository
     * @return \Dingo\Api\Http\Response
     */
    public function show($id, PermissionRepository $permissionRepository)
    {
        return $this->response
            ->item($permissionRepository->find($id), new PermissionTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *      path="/api/permissions",
     *      summary="Get all permissions",
     *      tags={"permissions"},
     *      description="Return all permissions
    <br> **permission:** _view-permission_",
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
     *                  @SWG\Items(ref="#/definitions/PermissionTransformerV1")
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
     * Return all permissions
     * @param PermissionRepository $permissionRepository
     * @return \Dingo\Api\Http\Response
     */
    public function index(PermissionRepository $permissionRepository)
    {
        return $this->response
            ->collection($permissionRepository->all(), new PermissionTransformer())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *      path="/api/permissions",
     *      summary="Create permission",
     *      tags={"permissions"},
     *      description="Create permission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="auth",
     *          in="body",
     *          description="User that should be authorization",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PermissionCreateRequestV1")
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
     * @param PermissionCreateRequest $permissionCreateRequest
     * @param PermissionRepository $permissionRepository
     * @return void
     */
    public function create(PermissionCreateRequest $permissionCreateRequest, PermissionRepository $permissionRepository)
    {
        $permissionRepository->create($permissionCreateRequest->all());
    }


    /**
     * @SWG\Put(
     *      path="/api/permissions",
     *      summary="Update permission",
     *      tags={"permissions"},
     *      description="Update permission",
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
     * @param PermissionRepository $permissionRepository
     * @return void
     */
    public function update(Request $request, $id, PermissionRepository $permissionRepository)
    {
        $permissionRepository->update($request->all(), $id);
    }
}
