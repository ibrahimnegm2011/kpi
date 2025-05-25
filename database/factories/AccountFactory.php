<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'contact_name' => fake()->name(),
            'contact_email' => fake()->email(),
            'contact_phone' => fake()->phoneNumber(),
            'created_by' => User::where('type', UserType::ADMIN())->first()?->id ?? User::factory()->create()->id,

        ];
    }
}
