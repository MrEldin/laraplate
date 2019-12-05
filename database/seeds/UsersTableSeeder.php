<?php

use Illuminate\Database\Seeder;
use SmartlyJobs\Entities\User\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(User::class)->create([
            'email'    => 'admin@mail.com',
            'password' => 'password'
        ]);

        $admin->assignRole('super-admin');
    }
}
