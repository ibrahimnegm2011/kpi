<?php

namespace App\Exports;

use App\Models\Forecast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerformanceReportExport implements FromCollection, withHeadings
{
    /**
     * @param  Collection<Forecast>  $data
     */
    public function __construct(public Collection $data) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data->map(function (Forecast $forecast) {
            return [
                'Category' => $forecast->kpi->category->name,
                'KPI' => $forecast->kpi->name,
                'Definition' => $forecast->kpi->definition,
                'Equation' => $forecast->kpi->equation,
                'Unit' => $forecast->kpi->unit_of_measurement,
                'Company' => $forecast->company->name,
                'Department' => $forecast->department->name,
                'Year' => $forecast->year,
                'Month' => Carbon::create()->month($forecast->month)->format('F'),
                'Target' => $forecast->target,
                'Value' => $forecast->value,
                'Percentage' => ($forecast->value && $forecast->target != 0)
                    ? round($forecast->value / $forecast->target * 100, 1).'%'
                    : '-',
                'Remarks' => $forecast->remarks,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Category', 'KPI',
            'Definition', 'Equation', 'Unit',
            'Company', 'Department',
            'Year', 'Month',
            'Target', 'Value', 'Percentage',
            'Remarks',
        ];
    }
}
