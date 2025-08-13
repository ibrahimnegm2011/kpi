<?php

namespace App\Http\Controllers\Agent;

use App\Enums\Permission;
use App\Models\Forecast;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardController extends AgentController
{
    public function index(Request $request)
    {
        $data = [];
        if($request->has('filter')) {
            $inputs = $request->validate([
                'filter.year' => ['nullable', 'integer', 'date_format:Y'],
                'filter.months.*' => ['nullable', 'integer', 'min:1', 'max:12'],
                'filter.department' => [
                    'required',
                    Rule::exists('agent_assignments', 'department_id')->where('account_id', session('selected_account'))
                ],
                'filter.company' => [
                    'required',
                    Rule::exists('agent_assignments', 'company_id')->where('account_id', session('selected_account'))
                ],
            ]);

            $data = Kpi::query()
                ->where('is_active', true)
                ->whereHas('forecasts', fn ($query) => $query
                    ->when($inputs['filter']['year'] ?? null, fn ($query, $year) => $query->where('year', $year))
                    ->when($inputs['filter']['months'] ?? null, fn ($query, $months) => $query->whereIn('month', $months))
                    ->when($inputs['filter']['department'] ?? null, fn ($query, $departmentId) => $query->where('department_id', $departmentId))
                    ->when($inputs['filter']['company'] ?? null, fn ($query, $companyId) => $query->where('company_id', $companyId))
                    ->forCurrentAgentAssignments()
                )->with('category')
                ->get();

            $data = $data->groupBy('category.name');
        }


        $companies = Auth::user()->agent_assignments->pluck('company.name', 'company_id')->toArray();
        $departments = Auth::user()->agent_assignments->pluck('department.name', 'department_id')->toArray();
        return view('agent.dashboard', compact('data', 'companies', 'departments'));
    }

    public function chart(Request $request)
    {
        $inputs = $request->validate([
            'year' => ['required', 'integer', 'date_format:Y'],
            'months.*' => ['nullable', 'integer', 'min:1', 'max:12'],
            'department' => ['required', Rule::exists('agent_assignments', 'department_id')->where('account_id', session('selected_account'))],
            'company' => ['required', Rule::exists('agent_assignments', 'company_id')->where('account_id', session('selected_account'))],
            'kpi_id' => ['required', Rule::exists('kpis', 'id')->where('account_id', session('selected_account'))],
        ]);

        $data = Forecast::query()
            ->forCurrentAgentAssignments()
            ->where('account_id', session('selected_account'))
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
            'value' => $data->sum('value'),
            'goal' => $data->sum('target'),
            'percent' => round(($data->sum('value') / $data->sum('target'))  * 100,2),
            'months' => $data->pluck('month'),
            'targets' => $data->pluck('target'),
            'values' => $data->pluck('value'),
        ]);
    }
}
