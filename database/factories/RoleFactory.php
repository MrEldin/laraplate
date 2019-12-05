<?php

use Faker\Generator as Faker;
use SmartlyJobs\Entities\Role\Models\Role;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Role::class, function (Faker $faker) {
    return [
        Role::NAME  => $faker->name,
        Role::LABEL => $faker->word,
    ];
});
