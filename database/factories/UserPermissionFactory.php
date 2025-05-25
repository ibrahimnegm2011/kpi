<?php

namespace Database\Factories;

use App\Enums\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPermissionFactory extends Factory
{
    protected $model = UserPermission::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'permission' => $this->faker->randomElement(Permission::adminPermissions()),
        ];
    }
}
