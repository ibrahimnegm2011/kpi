<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-bullseye mr-3"></i> Forecasts </h1>
            <div class="flex gap-3">
                <div class="inline-flex">
                    <form action="{{ route('account.forecasts.import') }}"
                          method="POST" enctype="multipart/form-data" class="flex items-center m-0 p-0">
                        @csrf
                        <input
                            id="import-file-input"
                            type="file"
                            name="import_file"
                            accept=".xls,.xlsx"
                            class="hidden"
                            onchange="this.form.submit()"
                        >
                        <a type="button"
                           onclick="document.getElementById('import-file-input').click();"
                           class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 flex items-center rounded-l-md border border-gray-300 border-r border-r-gray-200">
                            <i class="fas fa-upload mr-1"></i> Import
                        </a>
                        <a href="{{ route('account.forecasts.sample') }}"
                           class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-3 px-2 flex items-center justify-center rounded-r-md border border-gray-300 border-l-0 leading-2">
                            <i class="fas fa-download"></i>
                        </a>
                    </form>
                </div>


                <a class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('account.forecasts.create')}}">Add Forecast</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <div class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v2m0 4h.01m-.01-10a9 9 0 110 18 9 9 0 010-18z"/>
                    </svg>
                    Please fix the following errors:
                </div>
                <ul class="list-disc pl-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.closed') === null || request('filter.closed') === '' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setClosed('')">
                            All
                        </button>
                        <button type="button"
                                class="px-4 py-2 border-t border-b border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.closed') === '1' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setClosed('1')">
                            Closed
                        </button>
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.closed') === '0' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setClosed('0')">
                            Opened
                        </button>
                        <input type="hidden" name="filter[closed]" id="filterClosed" value="{{ request('filter.closed') }}">
                    </div>
                </div>

                <input type="submit" style="display:none"/>
            </form>
        </div>

        <div class="bg-white -mt-14 px-4 flex items-center justify-end gap-3">
            <form id="bulk-form" method="POST" action="{{ route('account.forecasts.bulk') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="redirect" value="{{ url()->full() }}">
                <input type="hidden" name="action" id="bulk-action" value="">
                <span id="selected-count" class="text-sm text-gray-600"></span>

                <button type="button"
                        id="btn-close-selected"
                        class="px-4 py-2 bg-primary-500 text-white font-bold rounded disabled:bg-primary-200 disabled:cursor-not-allowed"
                        disabled
                        onclick="submitBulk('open')">
                    Open Selected
                </button>
                <button type="button"
                        id="btn-open-selected"
                        class="px-4 py-2 bg-secondary-500 text-white rounded disabled:bg-secondary-200 disabled:cursor-not-allowed"
                        disabled
                        onclick="submitBulk('close')">
                    Close Selected
                </button>
            </form>
        </div>

        <div class="bg-white overflow-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead>
                <tr>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light w-10">
                        <input type="checkbox" id="select-all" class="text-secondary-500" />
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light">
                        KPI
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light">
                        Month
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light">
                        Department
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light">
                        Status
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light">
                        Actual
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light">
                        Target
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-grey-dark border-b border-grey-light"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($forecasts as $forecast)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light align-top">
                            <input type="checkbox"
                                   class="row-checkbox text-secondary-500"
                                   value="{{$forecast->id}}"
                                   onchange="onRowCheckboxChange(this)">
                        </td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->kpi->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{\Carbon\Carbon::create()->month($forecast->month)->year($forecast->year)->format('F, Y')}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->company->name}} <br/> {{$forecast->department->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->is_closed ? 'Closed' : 'Opened'}}</td>
                        <td class="py-4 px-6 border-b border-grey-light" title="{{$forecast->submitted_at}}">{{$forecast->is_submitted ? $forecast->value : '-'}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$forecast->target}}</td>

                        <td class="py-4 px-6 border-b border-grey-light ">
                            <a class="text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.view', $forecast->id)}}"> <i class="fas fa-eye"></i> </a>
                            @if(!$forecast->is_submitted)
                                <a class="text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.edit', $forecast->id)}}"> <i class="fas fa-edit"></i> </a>
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
                                    <a class="text-secondary-500 hover:text-primary-500" href="{{route('account.forecasts.download', $forecast)}}" target="_blank">
                                        <i class="fas fa-file-download"></i>
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 px-6 border-b border-grey-light">No forecasts found.</td>
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

            function setClosed(value) {
                document.getElementById('filterClosed').value = value;
                submitForm();
            }

            function confirmDelete(forecastTitle) {
                return confirm(`Are you sure you want to delete forecast "${forecastTitle}"? This action cannot be undone.`);
            }

            const selectAllEl = document.getElementById('select-all');
            const bulkForm = document.getElementById('bulk-form');
            const selectedCountEl = document.getElementById('selected-count');
            const btnClose = document.getElementById('btn-close-selected');
            const btnOpen = document.getElementById('btn-open-selected');

            function updateBulkState() {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

                // Clear existing hidden inputs
                Array.from(bulkForm.querySelectorAll('input[name="ids[]"]')).forEach(el => el.remove());

                // Append selected as hidden inputs
                selected.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkForm.appendChild(input);
                });

                // Update Select All state
                if (checkboxes.length > 0) {
                    const allChecked = selected.length === checkboxes.length;
                    selectAllEl.checked = allChecked;
                    selectAllEl.indeterminate = selected.length > 0 && !allChecked;
                } else {
                    selectAllEl.checked = false;
                    selectAllEl.indeterminate = false;
                }

                // Enable/disable buttons and show count
                const hasSelection = selected.length > 0;
                btnClose.disabled = !hasSelection;
                btnOpen.disabled = !hasSelection;
                selectedCountEl.textContent = hasSelection ? `${selected.length} selected` : '';
            }

            function onRowCheckboxChange() {
                updateBulkState();
            }

            if (selectAllEl) {
                selectAllEl.addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    checkboxes.forEach(cb => cb.checked = selectAllEl.checked);
                    updateBulkState();
                });
            }

            function submitBulk(action) {
                const existing = bulkForm.querySelector('#bulk-action');
                existing.value = action; // 'close' or 'open'
                const hasIds = bulkForm.querySelector('input[name="ids[]"]') !== null;
                if (!hasIds) {
                    alert('Please select at least one forecast.');
                    return;
                }
                bulkForm.submit();
            }

            // Initialize state on page load
            updateBulkState();
        </script>
    </x-slot:scripts>
</x-app-layout>
