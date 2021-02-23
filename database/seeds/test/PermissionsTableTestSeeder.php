<?php

use Illuminate\Database\Seeder;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;

class PermissionsTableTestSeeder extends Seeder
{
    public function run()
    {
        // reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $permissions = [
            //Roles
            [
                Permission::NAME       => 'view-dashboard-page',
                Permission::LABEL      => 'View dashboard page',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'view-profile-page',
                Permission::LABEL      => 'View profile page',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'view-survey',
                Permission::LABEL      => 'View survey',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'create-survey',
                Permission::LABEL      => 'Create survey',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'update-survey',
                Permission::LABEL      => 'Update survey',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'delete-survey',
                Permission::LABEL      => 'Delete survey',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'view-landing-page',
                Permission::LABEL      => 'View landing page',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'create-landing-page',
                Permission::LABEL      => 'Create landing page',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'update-landing-page',
                Permission::LABEL      => 'Update landing page',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'delete-landing-page',
                Permission::LABEL      => 'Delete landing page',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'manage-organisation-users',
                Permission::LABEL      => 'Manage organisation users',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'view-organisations',
                Permission::LABEL      => 'View organisations',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'create-organisation',
                Permission::LABEL      => 'Create organisation',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'update-organisation',
                Permission::LABEL      => 'Update organisation',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'create-role',
                Permission::LABEL      => 'Create role',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'view-role',
                Permission::LABEL      => 'View role',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'update-role',
                Permission::LABEL      => 'Update role',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],
            [
                Permission::NAME       => 'delete-role',
                Permission::LABEL      => 'Delete role',
                Permission::GUARD_NAME => config('auth.defaults.guard')
            ],

        ];

        app('db')->table('permissions')->insert($permissions);

        $allPermissions = Permission::all();
        $superAdminRole = Role::where('name', 'super-admin')->first();

        $superAdminRole->permissions()->attach($allPermissions);
    }
}
