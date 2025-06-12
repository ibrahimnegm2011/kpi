<?php

namespace App\Imports;

use AllowDynamicProperties;
use App\Models\Company;
use App\Models\Department;
use App\Models\Forecast;
use App\Models\Kpi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

#[AllowDynamicProperties] class ForecastsImport implements SkipsEmptyRows, ToCollection
{
    use Importable;

    public array $errors = [];

    public array $validRows = [];

    public bool $tooManyRows = false;

    public function collection(Collection $collection): void
    {
        $dataRows = $collection->skip(1); // skip header

        if ($dataRows->count() > 100) {
            $this->tooManyRows = true;

            return;
        }

        $seen = [];

        foreach ($dataRows as $line => $row) {

            $data = [
                'category' => trim($row[0] ?? ''),
                'kpi' => trim($row[1] ?? ''),
                'definition' => trim($row[2] ?? ''),
                'year' => $row[3] ?? null,
                'month' => trim($row[4] ?? ''),
                'company' => trim($row[5] ?? ''),
                'department' => trim($row[6] ?? ''),
                'target' => $row[7] ?? null,
            ];

            // normalize date
            $inputDate = $this->normalizeInputDate($data['month'], $data['year']);
            if (is_string($inputDate)) {
                $this->errors[$line][] = $inputDate;

                continue;
            }
            $data = array_merge($data, $inputDate);

            // check kpi exists
            $kpi = $this->getKpisMap()[strtolower($data['category'])][strtolower($data['kpi'])] ?? null;
            if (! $kpi) {
                $this->errors[$line][] = "KPI '{$data['kpi']}' is invalid or not related to category '{$data['category']}'";

                continue;
            }
            $data['kpi_id'] = $kpi->id;
            unset($data['kpi'], $data['definition'], $data['category']);

            // check company exists
            $data['company_id'] = $this->getCompaniesMap()[strtolower($data['company'])] ?? null;
            if (! $data['company_id']) {
                $this->errors[$line][] = "Invalid company: {$data['company']}";

                continue;
            }
            unset($data['company']);

            // check department exists
            $data['department_id'] = $this->getDepartmentsMap()[strtolower($data['department'])] ?? null;
            if (! $data['department_id']) {
                $this->errors[$line][] = "Invalid department: {$data['department']}";

                continue;
            }
            unset($data['department']);

            $validator = Validator::make($data, [
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|digits:4|min:'.date('Y'),
                'target' => 'required',
            ]);

            if ($validator->fails()) {
                $this->errors[$line] = $validator->errors()->all();

                continue;
            }

            $key = strtolower("{$data['company_id']}-{$data['department_id']}-{$data['month']}-{$data['year']}");
            if (isset($seen[$key])) {
                $this->errors[$line][] = 'Duplicate forecast in the file.';

                continue;
            }
            $seen[$key] = true;

            if (Forecast::where([
                'company_id' => $data['company_id'],
                'department_id' => $data['department_id'],
                'month' => $data['month'],
                'year' => $data['year'],
            ])->exists()) {
                $this->errors[$line][] = 'Forecast already exists.';

                continue;
            }

            $this->validRows[] = $data;
        }
    }

    protected function normalizeInputDate($month, $year): array|string
    {
        try {
            // Convert numeric month to name
            if (is_numeric($month)) {
                $month = Carbon::createFromDate(null, (int) $month)->format('F'); // "February"
            }

            $parsedDate = Carbon::parse("1 {$month} {$year}");

            return [
                'month' => $parsedDate->month, // 1–12
                'year' => $parsedDate->year,
            ];
        } catch (\Exception $e) {
            return 'Invalid month or year format.';
        }
    }

    protected function getCompaniesMap()
    {
        if (isset($this->companyMap)) {
            return $this->companyMap;
        }

        $this->companyMap = Company::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->all();

        return $this->companyMap;
    }

    protected function getDepartmentsMap()
    {
        if (isset($this->departmentMap)) {
            return $this->departmentMap;
        }

        $this->departmentMap = Department::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->all();

        return $this->departmentMap;
    }

    protected function getKpisMap()
    {
        if (isset($this->kpisMap)) {
            return $this->kpisMap;
        }

        $this->kpisMap = Kpi::with('category')->get()
            ->groupBy(fn ($kpi) => strtolower($kpi->category->name))
            ->map(fn ($group) => $group->keyBy(fn ($kpi) => strtolower($kpi->name)))
            ->all();

        return $this->kpisMap;
    }
}
