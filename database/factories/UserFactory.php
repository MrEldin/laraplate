<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laraplate\Entities\User\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Laraplate\Entities\User\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Laraplate\Entities\User\Models\User>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            User::FIRST_NAME     => fake()->firstName(),
            User::LAST_NAME      => fake()->lastName(),
            User::EMAIL          => fake()->unique()->safeEmail(),
            User::PASSWORD       => 'password',
            User::REMEMBER_TOKEN => Str::random(10),
        ];
    }
}
