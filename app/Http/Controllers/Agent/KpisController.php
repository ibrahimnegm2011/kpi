<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Forecast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class KpisController extends Controller
{
    public function index()
    {
        return view('agent.kpis.index', [
            'forecasts' => QueryBuilder::for(Forecast::class)->allowedFilters([
                AllowedFilter::exact('category', 'kpi.category_id'),
                AllowedFilter::exact('year', 'year'),
                AllowedFilter::exact('month', 'month'),
            ])
                ->where('company_id', Auth::user()->company_id)
                ->where('department_id', Auth::user()->department_id)
                ->where('is_submitted', false)
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function overdue()
    {
        return view('agent.kpis.overdue', [
            'forecasts' => QueryBuilder::for(Forecast::class)->allowedFilters([
                AllowedFilter::exact('category', 'kpi.category_id'),
                AllowedFilter::exact('year', 'year'),
                AllowedFilter::exact('month', 'month'),
            ])
                ->where('company_id', Auth::user()->company_id)
                ->where('department_id', Auth::user()->department_id)
                ->where('is_submitted', false)
                ->where('year', '<=', now()->year)
                ->where('month', '<', now()->month)
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function submitted()
    {
        return view('agent.kpis.submitted', [
            'forecasts' => QueryBuilder::for(Forecast::class)->allowedFilters([
                AllowedFilter::exact('category', 'kpi.category_id'),
                AllowedFilter::exact('year', 'year'),
                AllowedFilter::exact('month', 'month'),
            ])
                ->where('company_id', Auth::user()->company_id)
                ->where('department_id', Auth::user()->department_id)
                ->where('is_submitted', true)
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function form(Forecast $forecast)
    {
        return view('agent.kpis.form', compact('forecast'));
    }

    public function submit(Request $request, Forecast $forecast)
    {
        $data = $request->validate([
            'value' => ['required'],
            'evidence_filepath' => [
                'required',
                'file',
                'max:10240', // 10 MB max size
                'mimes:zip,pdf,doc,docx,jpg,jpeg,png'
            ],
            'remarks' => ['nullable', 'string'],
        ]);

        // Handle file upload and store in 'evidence' directory inside storage/app
        if ($request->hasFile('evidence_filepath')) {
            $file = $request->file('evidence_filepath');
            $extension = $file->getClientOriginalExtension();

            // Gather relevant info for filename
            $kpiTitle = $forecast->kpi->title ?? 'kpi';
            $company = $forecast->company->name ?? 'company';
            $department = $forecast->department->name ?? 'department';

            // Sanitize and build file name
            $filename = sprintf(
                '%s_%s_%s.%s',
                Str::slug($kpiTitle),
                Str::slug($company),
                Str::slug($department),
                $extension
            );

            $path = $file->storeAs('evidence', $filename);
            $data['evidence_filepath'] = $path; // store path in DB
        }

        $data['is_submitted'] = true;
        $data['submitted_by'] = Auth::id();
        $data['submitted_at'] = now();

         $forecast->update($data);

         return redirect(redirect(route('company_kpis')))->with('success', 'KPI has been Submitted successfully!');
    }
}
