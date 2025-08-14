<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-key mr-3"></i> Done KPIs </h1>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex items-center justify-items-center gap-3">
                    <select name="filter[category]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Categories</option>
                        @foreach(\App\Models\Category::forAccount(session('selected_account')) as $category)
                            <option value="{{$category->id}}" {{request('filter.category') !== $category->id ? '' : 'selected' }}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                    <select name="filter[year]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Years</option>
                        @php
                            $minYear = \App\Models\Forecast::min('year') ?? date('Y');
                            $maxYear = \App\Models\Forecast::max('year') ?? date('Y');
                        @endphp
                        @if(isset($minYear) && isset($maxYear))
                            @for($year = $minYear; $year <= $maxYear; $year++)
                                <option value="{{ $year }}" {{ request('filter.year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        @endif
                    </select>
                    <select name="filter[month]" class="max-w-xs w-full" onchange="submitForm()">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            @php
                                $monthName = \Carbon\Carbon::create()->month($m)->format('F');
                            @endphp
                            <option value="{{ $m }}" {{ request('filter.month') == $m ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="submit" style="display:none"/>
            </form>
        </div>
        <div class="bg-white overflow-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                <tr>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Kpi
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Category
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Department
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Month
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Target
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Value
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">

                    </th>
                </tr>
                </thead>
                <tbody>
                @if(! $forecasts->count())
                    <tr class="hover:bg-gray-100">
                        <td colspan="8" class="py-4 px-6 border-b border-grey-light"> No KPIs found. </td>
                    </tr>
                @endif
                @foreach($forecasts as $forecast)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->kpi->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->kpi->category->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->company->name}} - {{$forecast->department->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{\Carbon\Carbon::create()->month($forecast->month)->year($forecast->year)->format('F, Y')}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->target}}</td>
                        <td class="py-4 px-6 border-b border-grey-light" title="{{$forecast->submitted_at}}">{{$forecast->value}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">
                            @if($forecast->evidence_filepath)
                                <a class="mx-1 text-secondary-500 hover:text-primary-500"
                                   title="Download Evidence"
                                   href="{{route('agent.forecasts.download', $forecast)}}" target="_blank">
                                    <i class="fas fa-file-download"></i>
                                </a>
                            @endif

                            @if(! $forecast->is_closed)
                                <a class="mx-1 text-secondary-500 hover:text-primary-500" href="{{route('agent.kpi_submit_form', $forecast)}}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{$forecasts->links()}}
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function submitForm() {
                document.getElementById('search-form').submit()
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
