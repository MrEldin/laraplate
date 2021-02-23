<?php

use Illuminate\Database\Seeder;
use Laraplate\Entities\Role\Models\Role;

class   RolesTableTestSeeder extends Seeder
{
    public function run()
    {
        factory(Role::class)->create([
            Role::NAME       => 'super-admin',
            Role::LABEL      => 'Super admin',
            Role::GUARD_NAME => config('auth.defaults.guard'),
        ]);
    }
}
