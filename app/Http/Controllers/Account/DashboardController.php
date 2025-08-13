<?php

namespace App\Http\Controllers\Account;


use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Models\Forecast;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (! \Auth::user()->hasPermission(Permission::DASHBOARD())) {
            return view('dashboard');
        }

        $data = [];
        if($request->has('filter')) {
            $inputs = $request->validate([
                'filter.year' => ['nullable', 'integer', 'date_format:Y'],
                'filter.months.*' => ['nullable', 'integer', 'min:1', 'max:12'],
                'filter.department' => [
                    'required',
                    Rule::exists('departments', 'id')->where('account_id', \Auth::user()->account_id)
                ],
                'filter.company' => [
                    'required',
                    Rule::exists('companies', 'id')->where('account_id', \Auth::user()->account_id)
                ],
            ]);

            $data = Kpi::query()
                ->where('account_id', $request->user()->account_id)
                ->where('is_active', true)
                ->whereHas('forecasts', fn ($query) => $query
                    ->whereNotNull('submitted_at')
                    ->where('is_closed', true)
                    ->when($inputs['filter']['year'] ?? null, fn ($query, $year) => $query->where('year', $year))
                    ->when($inputs['filter']['months'] ?? null, fn ($query, $months) => $query->whereIn('month', $months))
                    ->when($inputs['filter']['department'] ?? null, fn ($query, $departmentId) => $query->where('department_id', $departmentId))
                    ->when($inputs['filter']['company'] ?? null, fn ($query, $companyId) => $query->where('company_id', $companyId))
                )->with('category')
                ->get();

            $data = $data->groupBy('category.name');
        }

        return view('account.dashboard', compact('data'));
    }

    public function chart(Request $request)
    {
        $inputs = $request->validate([
            'year' => ['required', 'integer', 'date_format:Y'],
            'months.*' => ['nullable', 'integer', 'min:1', 'max:12'],
            'department' => ['required', Rule::exists('departments', 'id')->where('account_id', \Auth::user()->account_id)],
            'company' => ['required', Rule::exists('companies', 'id')->where('account_id', \Auth::user()->account_id)],
            'kpi_id' => ['required', Rule::exists('kpis', 'id')->where('account_id', \Auth::user()->account_id)],
        ]);

        $data = Forecast::query()
            ->where('account_id', $request->user()->account_id)
            ->where('is_closed', true)
            ->where('year', $inputs['year'])
            ->where('department_id', $inputs['department'])
            ->where('company_id', $inputs['company'])
            ->where('kpi_id', $inputs['kpi_id'])
            ->when($inputs['months'] ?? null, fn ($query, $months) => $query->whereIn('month', $months))
            ->with('kpi')
            ->orderBy('month')
            ->get();

        return response()->json([
            'name' => $data[0]->kpi->name,
            'value' => $data->last()->value,
            'goal' => $data->last()->target,
            'percent' => round(($data->last()->value / $data->last()->target)  * 100,2),
            'months' => $data->pluck('month'),
            'targets' => $data->pluck('target'),
            'values' => $data->pluck('value'),
        ]);
    }
}
