<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laraplate\Entities\Role\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Laraplate\Entities\Role\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Laraplate\Entities\Role\Models\Role>
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Role::NAME       => fake()->unique()->name(),
            Role::LABEL      => fake()->word(),
            Role::GUARD_NAME => config('auth.defaults.guard'),
        ];
    }
}
