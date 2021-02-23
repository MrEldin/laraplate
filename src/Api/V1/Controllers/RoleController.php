<?php

namespace Laraplate\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laraplate\Api\V1\Requests\Role\RoleCreateRequest;
use Laraplate\Api\V1\Transformers\RoleTransformer;
use Laraplate\Entities\Role\Contracts\RoleRepository;

class RoleController extends Controller
{

    /**
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
