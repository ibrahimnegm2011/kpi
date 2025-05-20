@forelse($forecasts as $categoryName => $catForecasts)
    <tr x-show="open" @click="categoryOpen[{{$categoryName}}] = !categoryOpen[{{$categoryName}}]">
        <td colspan="{{count($months) + 1}}" class="text-left font-bold bg-gray-200 text-lg py-2 px-6 border-b pl-10">
            {{ $categoryName }}
        </td>
    </tr>
    <x-masters.kpis-rows :forecasts="$catForecasts" :months="$months"/>
@empty
    <tr>
        <td colspan="{{count($months) + 1}}" class="py-4 px-6 border-b border-grey-light">No Data found.</td>
    </tr>
@endforelse
