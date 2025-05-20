<?php

namespace App\Http\Controllers;

use App\Enums\MeasureUnit;
use App\Models\Forecast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ForecastsController extends Controller
{
    public function index()
    {
        return view('forecasts.index', [
            'forecasts' => QueryBuilder::for(Forecast::class)->allowedFilters([
                AllowedFilter::exact('category', 'kpi.category_id'),
                AllowedFilter::exact('company', 'company_id'),
                AllowedFilter::exact('department', 'department_id'),
                AllowedFilter::exact('year', 'year'),
                AllowedFilter::exact('month', 'month'),
                AllowedFilter::scope('submitted'),
            ])->paginate(10)->withQueryString(),
        ]);
    }

    public function show(Forecast $forecast)
    {
        return view('forecasts.view', compact('forecast'));
    }

    public function form(?Forecast $forecast = null)
    {
        if ($forecast && in_array($forecast->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('forecasts.form', compact('forecast'));
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
        ]);

        $date = Carbon::create()->month((int) $data['month'])->year((int) $data['year'])->day(1);
        if($date->isPast()) {
            throw ValidationException::withMessages(['month' => 'Month must be in the future.']);
        }

        $q = Forecast::where('kpi_id', $data['kpi_id'])
            ->where('company_id', $data['company_id'])
            ->where('department_id', $data['department_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month']);
        if($q->exists()) {
            throw ValidationException::withMessages(['kpi_id' => 'KPI already exists for this company, department and month.']);
        }

        $data['created_by'] = Auth::id();

        Forecast::create(Arr::except($data, ['category_id']));

        return redirect(route('forecasts.index'))->with(['success' => 'Forecast has been created successfully']);
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
        ]);


        $date = Carbon::create()->month((int) $data['month'])->year((int) $data['year'])->day(1);
        if($date->isPast()) {
            throw ValidationException::withMessages(['month' => 'Month must be in the future.']);
        }

        $q = Forecast::whereNot('id', $forecast->id)
            ->where('kpi_id', $data['kpi_id'])
            ->where('company_id', $data['company_id'])
            ->where('department_id', $data['department_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month']);
        if($q->exists()) {
            throw ValidationException::withMessages(['kpi_id' => 'KPI already exists for this company, department and month.']);
        }

        $forecast->update(Arr::except($data, ['category_id']));

        return redirect(route('forecasts.index'))->with(['success' => 'Forecast has been updated successfully']);
    }


    public function delete(Forecast $forecast)
    {
        if($forecast->is_submitted) {
            abort(403, 'Forecast has been already submitted.');
        }

        $forecast->delete();

        return redirect(route('forecasts.index'))->with(['success' => 'Forecast has been deleted successfully.']);
    }

    public function downloadEvidence(Forecast $forecast)
    {
        $filePath = $forecast->evidence_filepath;

        if (!Storage::exists($filePath)) {
            abort(404, 'File not found');
        }

        // Send the file as a download
        return Storage::download($filePath);
    }

}
