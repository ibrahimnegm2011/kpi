<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-crosshairs mr-3"></i> Performance Report {{request('filter.year') ?? now()->year}} </h1>
            <div class="flex gap-3">
                <a class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('account.master.export')}}@if(request()->getQueryString())?{{ request()->getQueryString() }}@endif">
                    Export
                </a>

            </div>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex items-center justify-items-center gap-3">
                    <select name="filter[year]" class="max-w-xs w-full" onchange="submitForm()">
                        @php
                            $minYear = \App\Models\Forecast::min('year') ?? date('Y');
                            $maxYear = \App\Models\Forecast::max('year') ?? date('Y');
                        @endphp
                        @if(isset($minYear) && isset($maxYear))
                            @for($year = $minYear; $year <= $maxYear; $year++)
                                <option value="{{ $year }}" {{ request('filter.year',  now()->year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        @endif
                    </select>
                    <select name="filter[category]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Categories</option>
                        @foreach(\App\Models\Category::forAccount() as $category)
                            <option value="{{$category->id}}" {{request('filter.category') !== $category->id ? '' : 'selected' }}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                    <select name="filter[company]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Companies</option>
                        @foreach(\App\Models\Company::forAccount() as $company)
                            <option value="{{$company->id}}" {{request('filter.company') !== $company->id ? '' : 'selected' }}>
                                {{$company->name}}
                            </option>
                        @endforeach
                    </select>
                    <select name="filter[department]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Departments</option>
                        @foreach(\App\Models\Department::forAccount() as $department)
                            <option value="{{$department->id}}" {{request('filter.department') !== $department->id ? '' : 'selected' }}>
                                {{$department->name}}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex flex-col min-w-16 items-center gap-1" id="toggle-variance">
                        <label>
                            <input type="radio" name="filter[analysis]" value="percent"  {{request('filter.analysis', 'percent') == 'percent' ? 'checked' : ''}}> %
                        </label>
                        <label>
                            <input type="radio" name="filter[analysis]" value="variance" {{request('filter.analysis', 'percent') == 'variance' ? 'checked' : ''}}> V
                        </label>
                    </div>


                </div>

                <input type="submit" style="display:none"/>
            </form>
        </div>
        <div class="bg-gray-50 border overflow-x-auto inset-shadow-sm rounded-lg">
            <table class="min-w-full border-collapse text-left">
                <thead>
                <tr class="border-b-2 border-grey-light">
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-x border-grey-light max-w-96 whitespace-nowrap text-ellipsis overflow-hidden">
                        KPI
                    </th>
                    @foreach($filteredMonths as $monthNo)
                        <th class="pt-4 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b  border-x border-grey-light whitespace-nowrap text-center">
                            <span> {{ \Carbon\Carbon::create()->month($monthNo)->format('F') }} </span>
                            <div class="w-full flex justify-center items-center gap-0 mt-1 h-full">
                                <div class="flex items-center justify-center w-20 h-12 border-x border-t px-4">Actual</div>
                                <div class="flex items-center justify-center w-20 h-12 border-x border-t px-4">Target</div>
                                <div class="{{request('filter.analysis', 'percent') != 'percent' ? 'hidden' : 'flex'}} items-center justify-center w-20 h-12 border-x border-t px-4 value-col-percent">%</div>
                                <div class="{{request('filter.analysis', 'percent') != 'variance' ? 'hidden' : 'flex'}} items-center justify-center w-20 h-12 border-x border-t px-4 value-col-variance">V</div>
                            </div>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @php
                    $hasCompany   = request('filter.company');
                    $hasDept      = request('filter.department');
                    $hasCategory  = request('filter.category');
                @endphp

                @if(!$hasCompany || !$hasDept)
                    <x-masters.companies-rows :forecasts="$forecasts" :months="$filteredMonths"/>
                @else
                    @if($hasCategory)
                        <x-masters.kpis-rows :forecasts="$forecasts" :months="$filteredMonths"/>
                    @else
                        <x-masters.categories-rows :forecasts="$forecasts" :months="$filteredMonths"/>
                    @endif
                @endif
                </tbody>
            </table>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function submitForm() {
                document.getElementById('search-form').submit()
            }

            document.getElementById('toggle-variance').addEventListener('change', function(e) {
                if (e.target.name === "filter[analysis]") {
                    const show = e.target.value;
                    // Percent columns
                    document.querySelectorAll('.value-col-percent').forEach(el => {
                        if (show === 'percent') {
                            el.classList.remove('hidden');
                            el.classList.add('flex');
                        } else {
                            el.classList.remove('flex');
                            el.classList.add('hidden');
                        }
                    });
                    // Variance columns
                    document.querySelectorAll('.value-col-variance').forEach(el => {
                        if (show === 'variance') {
                            el.classList.remove('hidden');
                            el.classList.add('flex');
                        } else {
                            el.classList.remove('flex');
                            el.classList.add('hidden');
                        }
                    });
                }
            });

        </script>
    </x-slot:scripts>
</x-app-layout>
