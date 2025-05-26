<?php

namespace Database\Factories;

use App\Enums\MeasureUnit;
use App\Enums\UserType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kpi>
 */
class KpiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'category_id' => Category::factory(),
            'definition' => fake()->sentence(),
            'equation' => fake()->sentence(),
            'unit_of_measurement' => fake()->word(),
            'symbol' => MeasureUnit::AMOUNT(),
            'created_by' => User::where('type', UserType::ADMIN())->first()?->id ?? User::factory()->create()->id,
        ];
    }
}
