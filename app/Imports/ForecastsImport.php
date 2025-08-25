<?php

namespace App\Imports;

use AllowDynamicProperties;
use App\Models\Company;
use App\Models\Department;
use App\Models\Forecast;
use App\Models\Kpi;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

#[AllowDynamicProperties] class ForecastsImport implements SkipsEmptyRows, ToCollection
{
    use Importable;

    public array $errors = [];

    public array $validRows = [];

    public array $seen = [];

    public bool $tooManyRows = false;

    public function collection(Collection $collection): void
    {
        $this->errors = [];
        $this->validRows = [];
        $this->seen = [];
        $this->tooManyRows = false;

        $dataRows = $collection->skip(1); // skip header

        if ($dataRows->count() > 2000) {
            $this->tooManyRows = true;

            return;
        }

        foreach ($dataRows as $line => $row) {

            $data = $this->validateRow($line, $row);

            if(!$data) continue;

            $data['account_id'] = Auth::user()->account_id;
            $data['created_by'] = Auth::user()->id;
            $data['updated_at'] = now();
            $data['created_at'] = now();

            $this->validRows[] = $data;
        }

        if(! empty($this->errors)){
            return;
        }

        DB::transaction(function(){
            collect($this->validRows)->chunk(100)->each(function ($chunk) {
                $rows = $chunk->map(function ($row) {
                    $row = is_array($row) ? $row : $row->toArray();
                    $row['id'] = (string) Str::ulid();
                    return $row;
                })->toArray();

                Forecast::insert($rows);
            });
        });
    }

    protected function validateRow($line, $row)
    {
        $data = [
            'category' => trim($row[0] ?? ''),
            'kpi' => trim($row[1] ?? ''),
            'year' => $row[2] ?? null,
            'month' => trim($row[3] ?? ''),
            'company' => trim($row[4] ?? ''),
            'department' => trim($row[5] ?? ''),
            'value' => $row[6],
            'target' => $row[7] ?? null,
            'is_submitted' => false,
            'submitted_at' => null,
            'submitted_by' => null,
        ];

        // normalize date
        $inputDate = $this->normalizeInputDate($data['month'], $data['year']);
        if (is_string($inputDate)) {
            $this->errors[$line][] = $inputDate;

            return false;
        }
        $data = array_merge($data, $inputDate);

        // check kpi exists
        $kpi = $this->getKpisMap()[strtolower($data['category'])][strtolower($data['kpi'])] ?? null;
        if (! $kpi) {
            $this->errors[$line][] = "KPI '{$data['kpi']}' is invalid or not related to category '{$data['category']}'";

            return false;
        }
        $data['kpi_id'] = $kpi->id;
        unset($data['kpi'], $data['category']);

        // check company exists
        $data['company_id'] = $this->getCompaniesMap()[strtolower($data['company'])] ?? null;
        if (! $data['company_id']) {
            $this->errors[$line][] = "Invalid company: {$data['company']}";

            return false;
        }
        unset($data['company']);

        // check department exists
        $data['department_id'] = $this->getDepartmentsMap()[strtolower($data['department'])] ?? null;
        if (! $data['department_id']) {
            $this->errors[$line][] = "Invalid department: {$data['department']}";

            return false;
        }
        unset($data['department']);

        if(! is_null($data['value'])){
            $data['is_submitted'] = true;
            $data['submitted_at'] = now();
            $data['submitted_by'] = auth()->id();
        }

        $validator = Validator::make($data, [
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4',
            'target' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors[$line] = $validator->errors()->all();

            return false;
        }

        $key = strtolower("{$data['kpi_id']}-{$data['company_id']}-{$data['department_id']}-{$data['month']}-{$data['year']}");
        if (isset($this->seen[$key])) {
            $this->errors[$line][] = 'Duplicate forecast in the file.';

            return false;
        }
        $this->seen[$key] = true;

        if (Forecast::where([
            'kpi_id' => $data['kpi_id'],
            'company_id' => $data['company_id'],
            'department_id' => $data['department_id'],
            'month' => $data['month'],
            'year' => $data['year'],
        ])->exists()) {
            $this->errors[$line][] = 'Forecast already exists.';

            return false;
        }

        return $data;
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
            Log::error($e->getMessage(), [$month, $year]);
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
