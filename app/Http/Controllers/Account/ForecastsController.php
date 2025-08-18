<?php

namespace App\Http\Controllers\Account;

use App\Enums\ClosedOption;
use App\Enums\ReminderOption;
use App\Http\Controllers\Controller;
use App\Imports\ForecastsImport;
use App\Models\Company;
use App\Models\Department;
use App\Models\Forecast;
use App\Models\Kpi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ForecastsController extends Controller
{
    public function index()
    {
        return view('account.forecasts.index', [
            'forecasts' => QueryBuilder::for(Forecast::class)->allowedFilters([
                AllowedFilter::exact('category', 'kpi.category_id'),
                AllowedFilter::exact('company', 'company_id'),
                AllowedFilter::exact('department', 'department_id'),
                AllowedFilter::exact('year', 'year'),
                AllowedFilter::exact('month', 'month'),
                AllowedFilter::scope('submitted'),
                AllowedFilter::scope('closed'),
            ])
                ->where('account_id', Auth::user()->account_id)
                ->whereHas('kpi', fn ($q) => $q->active(true))
                ->orderBy('is_closed')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->orderByDesc('created_at')
                ->latest()
                ->paginate(50)->withQueryString(),
        ]);
    }

    public function show(Forecast $forecast)
    {
        return view('account.forecasts.view', compact('forecast'));
    }

    public function form(?Forecast $forecast = null)
    {
        if ($forecast && in_array($forecast->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('account.forecasts.form', compact('forecast'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'kpi_id' => ['required', Rule::exists('kpis', 'id')],
            'company_id' => ['required', Rule::exists('companies', 'id')],
            'department_id' => ['required', Rule::exists('departments', 'id')],
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer'],
            'target' => ['required'],
            'auto_close_option' => ['nullable', Rule::enum(ClosedOption::class)],
            'auto_close_day' => ['nullable', 'min:1', 'max:31'],
            'reminder_option' => ['nullable', Rule::enum(ReminderOption::class)],
        ]);

//        $date = Carbon::create()->month((int) $data['month'])->year((int) $data['year'])->day(1);
//        if ($date->isPast()) {
//            throw ValidationException::withMessages(['month' => 'Month must be in the future.']);
//        }

        $q = Forecast::where('kpi_id', $data['kpi_id'])
            ->where('company_id', $data['company_id'])
            ->where('department_id', $data['department_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month']);
        if ($q->exists()) {
            throw ValidationException::withMessages(['kpi_id' => 'KPI already exists for this company, department and month.']);
        }

        $data['account_id'] = Auth::user()->account_id;
        $data['created_by'] = Auth::id();

        if($data['auto_close_option'] == ClosedOption::MANUALLY()){
            $data['auto_close_day'] = null;
        }

        Forecast::create(Arr::except($data, ['category_id']));

        return redirect(route('account.forecasts.index'))->with(['success' => 'Forecast has been created successfully']);
    }

    public function update(Forecast $forecast, Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'kpi_id' => ['required', Rule::exists('kpis', 'id')],
            'company_id' => ['required', Rule::exists('companies', 'id')],
            'department_id' => ['required', Rule::exists('departments', 'id')],
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer'],
            'target' => ['required'],
            'auto_close_option' => ['nullable', Rule::enum(ClosedOption::class)],
            'auto_close_day' => ['nullable', 'min:1', 'max:31'],
            'reminder_option' => ['nullable', Rule::enum(ReminderOption::class)],
            'is_closed' => ['sometimes', 'boolean'],
        ]);

//        $date = Carbon::create()->month((int) $data['month'])->year((int) $data['year'])->day(1);
//        if ($date->isPast()) {
//            throw ValidationException::withMessages(['month' => 'Month must be in the future.']);
//        }

        $q = Forecast::whereNot('id', $forecast->id)
            ->where('kpi_id', $data['kpi_id'])
            ->where('company_id', $data['company_id'])
            ->where('department_id', $data['department_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month']);
        if ($q->exists()) {
            throw ValidationException::withMessages(['kpi_id' => 'KPI already exists for this company, department and month.']);
        }

        if($data['auto_close_option'] == ClosedOption::MANUALLY()){
            $data['auto_close_day'] = null;
        }

        $data['is_closed'] = $request->boolean('is_closed');

        $forecast->update(Arr::except($data, ['category_id']));

        return redirect(route('account.forecasts.index'))->with(['success' => 'Forecast has been updated successfully']);
    }

    public function bulk_action(Request $request)
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['close', 'open'])],
            'ids' => ['required', 'array'],
            'ids.*' => ['required', Rule::exists('forecasts', 'id')->where('account_id', Auth::user()->account_id)],
        ]);

        $forecastsQ = Forecast::whereIn('id', $data['ids'])
            ->when($data['action'] == 'close', fn ($q) => $q->where('is_closed', false))
            ->when($data['action'] == 'open', fn ($q) => $q->where('is_closed', true));

        $count = $forecastsQ->count();

        if(!$count){
            return redirect(route('account.forecasts.index'))->with(['error' => "No forecasts found to be {$data['action']}ed."]);
        }

        $forecastsQ->update(['is_closed' => $data['action'] == 'close']);

        return redirect(route('account.forecasts.index'))->with(['success' => "{$count} forecasts have been {$data['action']}ed successfully."]);
    }

    public function delete(Forecast $forecast)
    {
        if ($forecast->is_submitted) {
            abort(403, 'Forecast has been already submitted.');
        }

        $forecast->delete();

        return redirect(route('account.forecasts.index'))->with(['success' => 'Forecast has been deleted successfully.']);
    }

    public function downloadEvidence(Forecast $forecast)
    {
        $filePath = $forecast->evidence_filepath;

        if (! Storage::exists($filePath)) {
            abort(404, 'File not found');
        }

        // Send the file as a download
        return Storage::download($filePath);
    }

    public function sample()
    {
        $kpisCategory = Kpi::byAccount(Auth::user()->account_id)->active(true)
            ->with('category')->get()
            ->pluck('category.name', 'name');
        $kpis = $kpisCategory->keys()->toArray();
        $companies = Company::byAccount(Auth::user()->account_id)->pluck('name')->toArray();
        $departments = Department::byAccount(Auth::user()->account_id)->pluck('name')->toArray();
        $months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        $maxCount = max(count($kpis), count($companies), count($departments), count($months));

        $data = collect();
        for ($i = 0; $i < $maxCount; $i++) {
            $data->push([
                'Category' => ($kpis[$i] ?? null) ? $kpisCategory[$kpis[$i]] : '',
                'KPI' => $kpis[$i] ?? '',
                'Year' => date('Y'),
                'Month' => $months[$i] ?? '',
                'Company' => $companies[$i] ?? '',
                'Department' => $departments[$i] ?? '',
                'Target' => '',
                'Value' => '',
            ]);
        }

        return Excel::download(new class($data) implements FromCollection, ShouldAutoSize, WithHeadings
        {
            public function __construct(protected $data) {}

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'Category', 'KPI', 'Year', 'Month', 'Company', 'Department', 'Target', 'Value',
                ];
            }
        }, 'forecasts-sample-'.date('YmdHis').'.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        $import = new ForecastsImport();
        Excel::import($import, $request->file('import_file'));

        if ($import->tooManyRows) {
            return back()->withErrors('The file cannot contain more than 100 rows.');
        }

        if (! empty($import->errors)) {
            $flatErrors = [];

            foreach ($import->errors as $line => $messages) {
                foreach ($messages as $message) {
                    $flatErrors[] = "Row {$line}: {$message}";
                }
            }

            return back()->withErrors($flatErrors);
        }

        foreach ($import->validRows as $row) {
            Forecast::create($row);
        }

        return back()->with('success', count($import->validRows).' forecasts imported successfully.');
    }
}
