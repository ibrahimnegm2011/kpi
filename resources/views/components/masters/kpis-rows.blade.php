@forelse($forecasts as $kpi => $monthsForecasts)
    <tr class="max-h-12 hover:bg-gray-100" x-show="open">
        <td class="py-4 px-6 border-b border-grey-light max-w-96 whitespace-nowrap overflow-hidden text-ellipsis">
            {{ $kpi }}
        </td>
        @foreach($months as $monthNo)
            @php
                $forecast = $monthsForecasts->where('month', $monthNo)->first();
                $target = floatval($forecast?->target);
                $value = $forecast?->value ? floatval($forecast?->value) : '-';
                $percentage = ($forecast?->value && $target != 0)
                    ? round($value / $target * 100, 1) . '%'
                    : '-';
                $variance = ($forecast?->value && $target != 0) ? (1 - round($value / $target, 2)) : '-';
            @endphp
            <td class="h-12 border-b border-grey-light whitespace-nowrap text-center align-middle">
                @if($forecast)
                    <div class="flex justify-center items-center gap-0 h-full">
                        <div class="w-20 h-full border-x px-4 flex items-center justify-center">
                            {{ $target }}
                        </div>
                        <div class="w-20 h-full border-x px-4 flex items-center justify-center">
                            {{ $value }}
                        </div>
                        <div class="{{request('filter.analysis', 'percent') != 'percent' ? 'hidden' : 'flex'}}  items-center justify-center w-20 h-full border-x px-4 value-col-percent">
                            {{ $percentage }}
                        </div>
                        <div class="{{request('filter.analysis', 'percent') != 'variance' ? 'hidden' : 'flex'}}  items-center justify-center w-20 h-full border-x px-4 value-col-variance">
                            {{ $variance }}
                        </div>
                    </div>
                @else
                    <div class="flex justify-center items-center gap-0 h-full text-gray-400">
                        <span>-</span>
                    </div>
                @endif
            </td>
        @endforeach
    </tr>
@empty
    <tr>
        <td colspan="{{ count($months) + 1 }}" class="py-4 px-6 border-b border-grey-light">No Data found.</td>
    </tr>
@endforelse
