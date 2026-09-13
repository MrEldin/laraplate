<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laraplate\Entities\Permission\Models\Permission;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Laraplate\Entities\Permission\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Laraplate\Entities\Permission\Models\Permission>
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Permission::NAME       => fake()->unique()->name(),
            Permission::LABEL      => fake()->word(),
            Permission::GUARD_NAME => config('auth.defaults.guard'),
        ];
    }
}
