@forelse($forecasts as $companyDepartmentName => $companyDepartmentForecasts)
    <tbody x-data="{ open: true, categoryOpen: {}}">
    <tr @click="open = !open" class="cursor-pointer">
        <td colspan="{{count($months) + 1}}" class="text-left font-bold bg-gray-200 text-lg py-2 px-6 border-b">
            <span x-text="open ? '▼' : '▶'" class="mr-2"></span>
            {{ $companyDepartmentName }}
        </td>
    </tr>
        @if(! request('filter.category'))
            <x-masters.categories-rows :forecasts="$companyDepartmentForecasts" :months="$months" x-if="open"/>
        @else
            <x-masters.kpis-rows :forecasts="$companyDepartmentForecasts" :months="$months" x-if="open"/>
        @endif
    </tbody>
@empty
    <tr>
        <td colspan="{{count($months) + 1}}" class="py-4 px-6 border-b border-grey-light">No Data found.</td>
    </tr>
@endforelse
