<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-bullseye mr-3"></i> Forecasts </h1>
            <div class="flex gap-3">
                <a class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('account.forecasts.create')}}">Add Forecast</a>
            </div>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex items-center justify-items-center gap-3">
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


                <div class="flex gap-3 mt-5">
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.submitted') === null || request('filter.submitted') === '' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setSubmitted('')">
                            All
                        </button>
                        <button type="button"
                                class="px-4 py-2 border-t border-b border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.submitted') === '1' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setSubmitted('1')">
                            Submitted
                        </button>
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.submitted') === '0' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setSubmitted('0')">
                            Not Submitted
                        </button>
                        <input type="hidden" name="filter[submitted]" id="filterSubmitted" value="{{ request('filter.submitted') }}">
                    </div>
                </div>

                <input type="submit" style="display:none"/>
            </form>
        </div>
        <div class="bg-white overflow-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                <tr>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        KPI
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Month
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Category
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Department
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Target
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Value
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($forecasts as $forecast)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->kpi->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{\Carbon\Carbon::create()->month($forecast->month)->year($forecast->year)->format('F, Y')}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->kpi->category->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->company->name}} <br/> {{$forecast->department->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->target}}</td>
                        <td class="py-4 px-6 border-b border-grey-light" title="{{$forecast->submitted_at}}">{{$forecast->is_submitted ? $forecast->value : '-'}}</td>

                        <td class="py-4 px-6 border-b border-grey-light ">
                            <a class="mx-1 text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.view', $forecast->id)}}"> <i class="fas fa-eye"></i> </a>
                            @if(!$forecast->is_submitted)
                                <a class="mx-1 text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.edit', $forecast->id)}}"> <i class="fas fa-edit"></i> </a>
                                <form method="POST" action="{{ route('account.forecasts.delete', $forecast) }}" style="display:inline;"
                                      onsubmit="return confirmDelete('{{ addslashes($forecast->title) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-secondary-500 hover:text-primary-500"
                                            title="Delete Forecast">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @else
                                @if($forecast->evidence_filepath)
                                    <a class="mx-1 text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.download', $forecast)}}" target="_blank">
                                        <i class="fas fa-file-download"></i>
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 px-6 border-b border-grey-light">No forecasts found.</td>
                    </tr>
                @endforelse
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

            function setSubmitted(value) {
                document.getElementById('filterSubmitted').value = value;
                submitForm();
            }

            function confirmDelete(forecastTitle) {
                return confirm(`Are you sure you want to delete forecast "${forecastTitle}"? This action cannot be undone.`);
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
