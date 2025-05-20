<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Department;
use App\Models\Forecast;
use App\Models\Kpi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ForecastFactory extends Factory
{
    protected $model = Forecast::class;

    public function definition(): array
    {
        return [
            'kpi_id' => Kpi::factory(),
            'company_id' => Company::factory(),
            'department_id' => Department::factory(),
            'year' => $this->faker->year(),
            'month' => $this->faker->month(),
            'target' => rand(1, 10) * 10,
            'is_submitted' => false,
            'created_by' => User::where('is_admin', true)->first()?->id ?? User::factory()->create()->id,
        ];
    }
}
