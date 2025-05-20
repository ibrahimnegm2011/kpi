<?php

namespace Database\Seeders;

use App\Enums\MeasureUnit;
use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\Forecast;
use App\Models\Kpi;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Ibrahim Negm',
            'email' => 'negm.ia@sirc.sa',
            'is_admin' => true,
        ]);

        Company::factory()->create(['name' => 'SIRC']);
        Company::factory()->create(['name' => 'REVIVA']);
        Company::factory()->create(['name' => 'AKAM']);
        Company::factory()->create(['name' => 'SAIL']);
        Company::factory()->create(['name' => 'AZYAT']);

        Department::factory()->create(['name' => 'IT']);
        Department::factory()->create(['name' => 'HR']);
        Department::factory()->create(['name' => 'Procurement']);
        Department::factory()->create(['name' => 'Business Development']);
        Department::factory()->create(['name' => 'Finance']);
        Department::factory()->create(['name' => 'Marketing']);
        Department::factory()->create(['name' => 'Public Relations']);

        $cat1 = Category::factory()->create(['name' => 'Branding']);
        $cat2 = Category::factory()->create(['name' => 'Recycling Projects']);
        $cat3 = Category::factory()->create(['name' => 'Revenue']);

        Kpi::factory()->create(['category_id' => $cat1->id, 'title' => 'Increase Number of Visits of Website']);
        Kpi::factory()->create(['category_id' => $cat2->id, 'title' => 'Increase Number of Projects']);
        Kpi::factory()->create(['category_id' => $cat3->id, 'title' => 'Increase Sales of Projects', 'measure_unit' => MeasureUnit::AMOUNT()]);

        if(app()->environment('local')) {
            $dates = [
                now()->subMonth(),
                now()->lastOfMonth(),
                now()->addMonths(2)
            ];
            $companies = Company::all();
            foreach ($companies as $company) {
                $departments = Department::all();
                foreach ($departments as $department) {

                    $user = User::factory()->create([
                        'name' => $company->name.' '.$department->name,
                        'email' => $company->name.'_'.$department->name.'@sirc.sa',
                        'company_id' => $company->id,
                        'department_id' => $department->id,
                        'position' => 'Manager',
                        'is_representative' => true,
                    ]);

                    $kpis = Kpi::all();
                    foreach ($kpis as $kpi) {
                        $date = Arr::random($dates);
                        $submissionFields = [];
                        if(Arr::random([true, false])){
                            $submissionFields = [
                                'is_submitted' => true,
                                'submitted_at' => now(),
                                'submitted_by' => $user->id,
                                'value' => rand(1, 10) * 10,
                                'remarks' => fake()->sentence(),
                                'evidence_filepath' => 'evidence/increase-number-of-projects_sirc_hr.pdf',
                            ];
                        }

                        Forecast::factory()->create([
                            'kpi_id' => $kpi->id,
                            'company_id' => $company->id,
                            'department_id' => $department->id,
                            'year' => $date->year,
                            'month' => $date->month,
                            'target' => rand(1, 10) * 10,
                            ...$submissionFields,
                        ]);
                    }
                }
            }
        }
    }
}
