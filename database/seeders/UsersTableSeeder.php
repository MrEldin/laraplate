<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Laraplate\Entities\User\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::factory()->create([
            'email'    => 'admin@mail.com',
            'password' => 'password'
        ]);

        $admin->assignRole('super-admin');
    }
}
