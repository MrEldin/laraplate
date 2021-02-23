<?php
namespace Database\Seeders;
use Laraplate\Entities\Role\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        factory(Role::class)->create([
            Role::NAME       => 'super-admin',
            Role::LABEL      => 'Super admin',
            Role::GUARD_NAME => config('auth.defaults.guard'),
        ]);

        factory(Role::class)->create([
            Role::NAME       => 'applicant',
            Role::LABEL      => 'Applicant',
            Role::GUARD_NAME => config('auth.defaults.guard'),
        ]);

        factory(Role::class)->create([
            Role::NAME       => 'hiring-manager',
            Role::LABEL      => 'Hiring Manager',
            Role::GUARD_NAME => config('auth.defaults.guard'),
        ]);
    }
}
