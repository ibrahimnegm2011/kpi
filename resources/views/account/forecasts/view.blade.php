<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('account.forecasts.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">View "{{$forecast->kpi->name}}" Forecast</h1>
    </div>

    <div class="overflow-auto mt-5">
        @php
            $fields = [
                'KPI' => $forecast->kpi->name,
                'Category' => $forecast->kpi->category->name,
                'Definition' => $forecast->kpi->definition,
                'Equation' => $forecast->kpi->equation,
                'Unit of Measurement' => $forecast->kpi->unit_of_measurement,
                'Company' => $forecast->company->name,
                'Department' => $forecast->department->name,
                'Month' => \Carbon\Carbon::create()->month($forecast->month)->year($forecast->year)->format('F, Y'),
                'Target' => "$forecast->target " . ($forecast->kpi->symbol ?? ''),
                'Submitted' => $forecast->is_submitted ? 'Yes' : 'No',
            ];

            if($forecast->is_submitted){
                $fields['Value'] = "$forecast->value " . ($forecast->kpi->symbol ?? '');
                $fields['Remarks'] = $forecast->remarks;
                $fields['Submitted At'] = $forecast->submitted_at;
                $fields['Submitted By'] = $forecast->submitter->name;
            }
            $i = 0;
        @endphp
        <div class="grid grid-cols-2 gap-y-2 gap-1">
            @foreach($fields as $key => $value)
                <div class="grid grid-cols-2 p-3 bg-[#7473b6] @if($i%4 > 1) bg-opacity-10 @else bg-opacity-20 @endif">
                    <div class="font-bold">{{$key}}</div>
                    <div>{!! $value !!}</div>
                </div>
                @php $i++; @endphp
            @endforeach
            @if($forecast->evidence_filepath)
                <div class="grid grid-cols-2 p-3 bg-[#7473b6] @if($i%4 > 1) bg-opacity-10 @else bg-opacity-20 @endif">
                    <div class="font-bold">Evidence</div>
                    <div>
                        <a class="mx-2 text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.download', $forecast)}}" target="_blank">
                            <i class="fas fa-file-download"></i> Download
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
