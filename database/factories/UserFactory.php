<?php
namespace Database\Factories;

use Faker\Generator as Faker;
use Laraplate\Entities\User\Models\User;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        User::FIRST_NAME     => $faker->firstName,
        User::LAST_NAME      => $faker->lastName,
        User::EMAIL          => $faker->unique()->safeEmail,
        User::PASSWORD       => 'password', // password
        User::REMEMBER_TOKEN => str_random(10),
    ];
});
