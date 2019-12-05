<?php

use Illuminate\Database\Seeder;

class TestingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableTestSeeder::class);
        $this->call(PermissionsTableTestSeeder::class);
    }
}
