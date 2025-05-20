<?php

namespace Database\Factories;

use App\Enums\MeasureUnit;
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
            'title' => fake()->name(),
            'category_id' => Category::factory()->create(),
            'description' => fake()->sentence(),
            'measure_unit' => MeasureUnit::NUMBER(),
            'created_by' => User::where('is_admin', true)->first()?->id ?? User::factory()->create()->id,
        ];
    }
}
