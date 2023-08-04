<?php

namespace Database\Factories;

use App\Domain\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Permission\Models\Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'is_admin' => false,
        ];
    }
}
