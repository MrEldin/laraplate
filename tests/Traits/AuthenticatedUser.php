<?php

namespace Tests\Traits;


use SmartlyJobs\Entities\User\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait AuthenticatedUser
{
    /**
     * @var User
     */
    public $authenticatedUser;

    public function createAuthenticatedUser($role = 'super-admin')
    {
        $this->authenticatedUser = factory(User::class)->create([
            User::EMAIL       => 'admin@smartlyjobs.app',
            User::PASSWORD    => 'password',
        ]);

        $this->authenticatedUser->assignRole($role);

        $this->actingAs($this->authenticatedUser);
    }

    protected function getRequestHeaders()
    {
        $token = JWTAuth::fromUser($this->authenticatedUser);

        $requestHeaders['HTTP_Authorization'] = 'Bearer ' . $token;

        return $requestHeaders;
    }

    /**
     * Assign permission to the user role
     *
     * @param $permission
     */
    public function givePermission($permission)
    {
        $this->authenticatedUser->role->givePermissionTo($permission);
    }
}
