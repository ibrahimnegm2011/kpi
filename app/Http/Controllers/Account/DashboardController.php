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
                'filter.months' => ['json'],
                'filter.department' => [
                    'required',
                    Rule::exists('departments', 'id')->where('account_id', \Auth::user()->account_id)
                ],
                'filter.company' => [
                    'required',
                    Rule::exists('companies', 'id')->where('account_id', \Auth::user()->account_id)
                ],
            ]);
            $inputs['filter']['months'] = json_decode($inputs['filter']['months']) ?? [];

            $data = Kpi::query()
                ->where('account_id', $request->user()->account_id)
                ->where('is_active', true)
                ->whereHas('forecasts', fn ($query) => $query
                    ->when($inputs['filter']['year'] ?? null, fn ($query, $year) => $query->where('year', $year))
                    ->when($inputs['filter']['months'], fn ($query, $months) => $query->whereIn('month', $months))
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
            'months' => ['json'],
            'department' => ['required', Rule::exists('departments', 'id')->where('account_id', \Auth::user()->account_id)],
            'company' => ['required', Rule::exists('companies', 'id')->where('account_id', \Auth::user()->account_id)],
            'kpi_id' => ['required', Rule::exists('kpis', 'id')->where('account_id', \Auth::user()->account_id)],
        ]);
        $inputs['months'] = json_decode($inputs['months']) ?? [];

        $data = Forecast::query()
            ->where('account_id', $request->user()->account_id)
            ->where('year', $inputs['year'])
            ->where('department_id', $inputs['department'])
            ->where('company_id', $inputs['company'])
            ->where('kpi_id', $inputs['kpi_id'])
            ->when($inputs['months'], fn ($query, $months) => $query->whereIn('month', $months))
            ->with('kpi')
            ->orderBy('month')
            ->get();

        $percent = $data->sum('target') == 0 ? 0 : round(($data->sum('value') / $data->sum('target'))  * 100,2);
        return response()->json([
            'name' => $data[0]->kpi->name,
            'value' => $data->sum('value'),
            'goal' => $data->sum('target'),
            'percent' => $percent,
            'months' => $data->pluck('month'),
            'targets' => $data->pluck('target'),
            'values' => $data->pluck('value'),
        ]);
    }
}
