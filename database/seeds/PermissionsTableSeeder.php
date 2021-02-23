<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;

class PermissionsTableSeeder extends Seeder
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
            ]
        ];

        app('db')->table('permissions')->insert($permissions);

        $allPermissions = Permission::all();
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $applicantRole = Role::where('name', 'applicant')->first();
        $hiringManager = Role::where('name', 'hiring-manager')->first();

        $superAdminRole->permissions()->attach($allPermissions);
        $applicantRole->permissions()->attach($allPermissions->whereIn(Permission::NAME, [
            'view-dashboard-page',
            'view-profile-page'
        ]));
        $hiringManager->permissions()->attach($allPermissions->whereIn(Permission::NAME, [
            'view-dashboard-page',
            'view-profile-page',
            'view-organisations',
            'view-campaign',
            'create-campaign',
            'update-campaign',
            'delete-campaign',
            'view-landing-page',
            'create-landing-page',
            'update-landing-page',
            'delete-landing-page',
        ]));
    }
}
