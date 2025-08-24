<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\UserType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\Forecast;
use App\Models\Kpi;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Ibrahim Negm',
            'email' => 'negm.ia@sirc.sa',
        ]);

        foreach (Permission::adminPermissions() as $permission) {
            UserPermission::factory()->create([
                'user_id' => $user->id,
                'permission' => $permission,
            ]);
        }

        $this->addAccount1();
        $this->addAccount2();
    }

    protected function addAccount1()
    {
        $account = Account::factory()->create([
            'name' => 'IT Account',
            'contact_name' => 'Account 1',
            'contact_email' => 'account1@sirc.sa',
            'contact_phone' => '+96657838778',
        ]);

        $accUser = User::factory()->create([
            'account_id' => $account->id,
            'name' => 'Account 1',
            'email' => 'account1@sirc.sa',
            'type' => UserType::ACCOUNT(),
        ]);
        $account->update(['admin_user_id' => $accUser->id]);

        foreach (Permission::accountPermissions() as $permission) {
            UserPermission::factory()->create([
                'user_id' => $accUser->id,
                'permission' => $permission,
            ]);
        }

        Company::factory()->create(['name' => 'SIRC', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'REVIVA', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'AKAM', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'SAIL', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'AZYAT', 'account_id' => $account->id]);

        Department::factory()->create(['name' => 'IT', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'HR', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Procurement', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Business Development', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Finance', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Marketing', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Public Relations', 'account_id' => $account->id]);

        $cat1 = Category::factory()->create(['name' => 'Branding', 'account_id' => $account->id]);
        $cat2 = Category::factory()->create(['name' => 'Recycling Projects', 'account_id' => $account->id]);
        $cat3 = Category::factory()->create(['name' => 'Revenue', 'account_id' => $account->id]);

        Kpi::factory()->create([
            'account_id' => $account->id, 'category_id' => $cat1->id, 'name' => 'Increase Number of Visits of Website',
            'definition' => 'Increase Number of Visits of Website', 'equation' => '',
            'unit_of_measurement' => 'Number of Visits', 'symbol' => 'V',
        ]);
        Kpi::factory()->create([
            'account_id' => $account->id, 'category_id' => $cat2->id, 'name' => 'Increase Number of Projects',
            'definition' => 'Increase Number of Projects', 'equation' => '',
            'unit_of_measurement' => 'Number of Projects', 'symbol' => 'P',
        ]);
        Kpi::factory()->create([
            'account_id' => $account->id, 'category_id' => $cat3->id, 'name' => 'Increase Sales of Projects',
            'definition' => 'Increase Sales of Projects', 'equation' => '',
            'unit_of_measurement' => 'Amount of Revenue', 'symbol' => 'SAR',
        ]);

        Kpi::factory()->create([
            'account_id' => $account->id, 'category_id' => $cat1->id, 'name' => 'Number of Sites Upgrades',
            'definition' => 'Increase Sales of Projects', 'equation' => '',
            'unit_of_measurement' => 'Amount of Revenue', 'symbol' => 'SAR',
        ]);

        if (app()->environment('local')) {
            $dates = [
                now()->subMonth(),
                now()->lastOfMonth(),
                now()->addMonths(2),
            ];
            $companies = Company::forAccount($account->id);
            foreach ($companies as $company) {
                $departments = Department::forAccount($account->id);
                foreach ($departments as $department) {

                    $user = User::factory()->create([
                        'name' => $company->name.' '.$department->name,
                        'email' => strtolower($company->name.'_'.$department->name).'@sirc.sa',
                        'type' => UserType::AGENT(),
                    ]);
                    $user->agent_assignments()->create([
                        'account_id' => $account->id,
                        'company_id' => $company->id,
                        'department_id' => $department->id,
                        'position' => 'Manager',
                        'created_by' => $accUser->id,
                    ]);

                    $kpis = Kpi::forAccount($account->id);
                    foreach ($kpis as $kpi) {
                        $date = Arr::random($dates);
                        $submissionFields = [];
                        if (Arr::random([true, false])) {
                            $submissionFields = [
                                'is_submitted' => true,
                                'submitted_at' => now(),
                                'submitted_by' => $user->id,
                                'is_closed' => Arr::random([true, false]),
                                'value' => rand(1, 10) * 10,
                                'remarks' => fake()->sentence(),
                                'evidence_filepath' => 'evidence/increase-number-of-projects_sirc_hr.pdf',
                            ];
                        }

                        Forecast::factory()->create([
                            'account_id' => $account->id,
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

    protected function addAccount2()
    {
        $account = Account::factory()->create([
            'name' => 'Operations Account',
            'contact_name' => 'Account 2',
            'contact_email' => 'account2@sirc.sa',
            'contact_phone' => '+96657838778',
        ]);

        $accUser = User::factory()->create([
            'account_id' => $account->id,
            'name' => 'Account 2',
            'email' => 'account2@sirc.sa',
            'type' => UserType::ACCOUNT(),
        ]);
        $account->update(['admin_user_id' => $accUser->id]);

        foreach (Permission::accountPermissions() as $permission) {
            UserPermission::factory()->create([
                'user_id' => $accUser->id,
                'permission' => $permission,
            ]);
        }

        Company::factory()->create(['name' => 'AKAM', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'EADA', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'MASAB', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'REVIVA', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'SAIL', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'SIRC', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'Sustainability Solutions', 'account_id' => $account->id]);
        Company::factory()->create(['name' => 'Tajmee', 'account_id' => $account->id]);

        Department::factory()->create(['name' => 'CB', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Operation', 'account_id' => $account->id]);
        Department::factory()->create(['name' => 'Finance', 'account_id' => $account->id]);

        $catCB = Category::factory()->create(['name' => 'CB', 'account_id' => $account->id]);
        $catFI = Category::factory()->create(['name' => 'Finance', 'account_id' => $account->id]);
        $catOP = Category::factory()->create(['name' => 'Operation', 'account_id' => $account->id]);

        $cbKPI = [
            'Cybersecurity  Effectiveness',
            'Employee Engagement Surveys',
            'ERP implementation',
            'Local Content',
            'Number of Employees',
            'Number of Saudis Full Time Employee',
            'Saudization Rate',
        ];
        $finKPIs = [
            'EBIT (Operating Profit)',
            'EBITDA',
            'Revenue',
            'Working Capital Ratio',
        ];
        $opKPIs = [
            'KSA Landfill Diverted Amount',
            'KSA Landfill Diverted Rate',
            'Lost-time injuries (LTI)',
            'Near Miss Incident (NMI)',
            'Nnmber of fatalities',
            'Recycled Amount',
            'Recycling Rate',
            'Total amount Waste Diverted from Landfill',
            'Total Collected Waste',
            'Total rate Waste Diverted from Landfill',
        ];

        foreach ($cbKPI as $kpi) {
            Kpi::factory()->create([
                'account_id' => $account->id, 'category_id' => $catCB->id, 'name' => $kpi, 'definition' => $kpi,
            ]);
        }

        foreach ($finKPIs as $kpi) {
            Kpi::factory()->create([
                'account_id' => $account->id, 'category_id' => $catFI->id, 'name' => $kpi, 'definition' => $kpi,
            ]);
        }

        foreach ($opKPIs as $kpi) {
            Kpi::factory()->create([
                'account_id' => $account->id, 'category_id' => $catOP->id, 'name' => $kpi, 'definition' => $kpi,
            ]);
        }
    }
}
