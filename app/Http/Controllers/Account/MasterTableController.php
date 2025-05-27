<?php

namespace App\Http\Controllers\Account;

use App\Exports\PerformanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Forecast;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class MasterTableController extends Controller
{
    protected function filterForecasts()
    {
        $forecastsQ = Forecast::with(['kpi.category', 'department', 'company'])
            ->where('account_id', auth()->user()->account_id)
            ->where('year', request('filter.year') ?? now()->year);
        if (request('filter.company')) {
            $forecastsQ->where('company_id', request('filter.company'));
        }
        if (request('filter.department')) {
            $forecastsQ->where('department_id', request('filter.department'));
        }
        if (request('filter.category')) {
            $forecastsQ->whereHas('kpi', fn ($q) => $q->where('category_id', request('filter.category')));
        }

        return $forecastsQ
            ->orderBy('month', 'desc')
            ->orderBy('company_id')
            ->get();
    }

    protected function groupingForecasts($forecasts)
    {
        if (! request('filter.company') || ! request('filter.department')) {
            $forecasts = $forecasts
                ->groupBy(function ($item) {
                    if (request('filter.company')) {
                        return $item->department->name;
                    }
                    if (request('filter.department')) {
                        return $item->company->name;
                    }

                    return $item->company->name.' ('.$item->department->name.')';
                })
                ->map(function (Collection $companyDepartmentGroup) {
                    if (! request('filter.category')) {
                        return $companyDepartmentGroup->groupBy(fn (Forecast $item) => $item->kpi->category->name)
                            ->map(function (Collection $categoryGroup) {
                                return $categoryGroup->groupBy(fn (Forecast $item) => $item->kpi->name);
                            });
                    } else {
                        return $companyDepartmentGroup->groupBy(fn (Forecast $item) => $item->kpi->name);
                    }
                });
        } else {
            if (! request('filter.category')) {
                $forecasts = $forecasts
                    ->groupBy(fn (Forecast $item) => $item->kpi->category->name)
                    ->map(function (Collection $categoryGroup) {
                        return $categoryGroup->groupBy(fn (Forecast $item) => $item->kpi->name);
                    });
            } else {
                $forecasts = $forecasts->groupBy(fn (Forecast $item) => $item->kpi->name);
            }
        }

        return $forecasts;
    }

    public function index()
    {
        $forecasts = $this->filterForecasts();

        $filteredMonths = $forecasts->pluck('month')->unique();

        $forecasts = $this->groupingForecasts($forecasts);

        return view('account.master.index', compact('forecasts', 'filteredMonths'));
    }

    public function export()
    {
        $forecasts = $this->filterForecasts();

        // export
        $filename = 'performance-report-'.now()->format('YmdHis').'.xlsx';

        return Excel::download(new PerformanceReportExport($forecasts), $filename);
    }
}
