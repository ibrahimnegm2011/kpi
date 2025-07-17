@php use App\Enums\ClosedOption;use App\Enums\ReminderOption; @endphp
<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('account.forecasts.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">{{$forecast ? 'Update "'.($forecast->kpi->name). '" Forecast' : 'Add New Forecast'}}</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{$forecast ? route('account.forecasts.update', $forecast->id) : route('account.forecasts.store')}}"
              method="post">
            @csrf
            @method($forecast ? 'PUT' : 'POST')

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-category">
                        Category*
                    </label>
                    <select id="grid-category" name="category_id"
                            class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('category_id', $forecast?->kpi->category_id) ? '' : 'selected'}} disabled>Select
                            Category
                        </option>
                        @foreach(\App\Models\Category::forAccount() as $category)
                            <option
                                value="{{$category->id}}" {{old('category_id', $forecast?->kpi->category_id) == $category->id ? 'selected' : '' }}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-kpi">
                        KPI*
                    </label>
                    <select id="grid-kpi" name="kpi_id"
                            class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('kpi_id', $forecast?->kpi_id) ? '' : 'selected'}} disabled>Select KPI</option>
                        @php
                            $category = old('category_id', $forecast?->kpi->category_id);
                            $kpis = $category ? \App\Models\Kpi::where('category_id', $category)->get() : collect();
                        @endphp
                        @foreach($kpis as $kpi)
                            <option
                                value="{{$kpi->id}}" {{old('kpi_id', $forecast?->kpi_id) == $kpi->id ? 'selected' : '' }}>
                                {{$kpi->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('kpi_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>


                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-company">
                        Company*
                    </label>
                    <select id="grid-company" name="company_id"
                            class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('company_id', $forecast?->company_id) ? '' : 'selected'}} disabled>Select
                            Company
                        </option>
                        @foreach(\App\Models\Company::forAccount() as $company)
                            <option
                                value="{{$company->id}}" {{old('company_id', $forecast?->company_id) == $company->id ? 'selected' : '' }}>
                                {{$company->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-department">
                        Department*
                    </label>
                    <select id="grid-department" name="department_id"
                            class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('department_id', $forecast?->department_id) ? '' : 'selected'}} disabled>Select
                            Department
                        </option>
                        @foreach(\App\Models\Department::forAccount() as $department)
                            <option
                                value="{{$department->id}}" {{old('department_id', $forecast?->department_id) == $department->id ? 'selected' : '' }}>
                                {{$department->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-year">
                        Year*
                    </label>
                    <select id="grid-year" name="year"
                            class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('year', $forecast?->year) ? '' : 'selected'}} disabled>Select Year</option>

                        @for($year = $forecast?->year ?? now()->year; $year <= now()->addYear()->year; $year++)
                            <option value="{{$year}}" {{old('year', $forecast?->year) == $year ? 'selected' : '' }}>
                                {{$year}}
                            </option>
                        @endfor
                    </select>
                    @error('year') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-month">
                        Month*
                    </label>
                    <select id="grid-month" name="month"
                            class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('month', $forecast?->month) ? '' : 'selected'}} disabled>Select Month</option>
                        @foreach(range(1, 12) as $m)
                            @php
                                $monthName = \Carbon\Carbon::create()->month($m)->format('F');
                            @endphp
                            <option value="{{ $m }}" {{ old('month', $forecast?->month) == $m ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                    @error('month') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-target">
                        Target*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-target" name="target" type="text" placeholder="Target..."
                        value="{{old('target', $forecast?->target)}}">
                    @error('target') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full flex">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        @php
                            $options = ClosedOption::cases();
                            $selected = old('auto_close_option', $forecast->auto_close_option ?? ClosedOption::MANUALLY());
                            $monthDayValue = old('auto_close_day', $forecast->auto_close_day ?? 25);
                        @endphp

                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Auto Close
                            Option</label>
                        <div x-data="{ autoCloseOption: '{{ $selected }}' }" class="mt-1 p-3 border rounded-md">
                            <div class="flex flex-col space-y-2">
                                @foreach($options as $option)
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="auto_close_option" value="{{ $option->value }}"
                                               @if($selected == $option->value) checked @endif
                                               x-model="autoCloseOption">
                                        <span class="ml-2">{{ $option->title() }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-4" x-transition
                                 x-show="autoCloseOption == '{{ ClosedOption::MONTH_DAY() }}'">
                                <label for="grid-close_day"
                                       class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                                    Day of Month
                                </label>
                                <input type="number" min="1" max="31" name="auto_close_day" id="grid-close_day"
                                       value="{{ $monthDayValue }}" class="form-input mt-1 block w-1/2"/>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        @php
                            $reminderOptions = ReminderOption::cases();
                            $reminderSelected = old('reminder_option', $forecast->reminder_option ?? ReminderOption::BASIC());
                        @endphp

                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Reminder
                            Option</label>
                        <div x-data="{ reminderOption: '{{ $reminderSelected }}' }" class="mt-1 p-3 border rounded-md">
                            <div class="flex flex-col space-y-2">
                                @foreach($reminderOptions as $reminder)
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="reminder_option" value="{{ $reminder->value }}"
                                               @if($reminderSelected == $reminder->value) checked @endif
                                               x-model="reminderOption">
                                        <span class="ml-2">{{ $reminder->title() }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>


                </div>

                @if($forecast)
                    <div class="w-full flex justify-center items-center pt-3">
                        <div class="flex items-center dark:border-gray-700">
                            <input id="closed-checkbox" type="checkbox" value="1" name="is_closed"
                                   {{old('is_closed', $forecast->is_closed) ? 'checked' : '' }}
                                   class="w-7 h-7 text-emerald-400 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="closed-checkbox"
                                   class="w-full py-4 ms-2 font-bold text-gray-900 dark:text-gray-300">Close</label>
                        </div>
                    </div>
                @endif
            </div>
            <div class="md:flex md:items-center">
                <button
                    class="m-auto shadow bg-emerald-400 hover:bg-emerald-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                    type="submit">
                    {{$forecast ? 'Save' : 'Create'}}
                </button>
            </div>
        </form>
    </div>


    <x-slot:scripts>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const categorySelect = document.getElementById('grid-category');
                const kpiSelect = document.getElementById('grid-kpi');

                categorySelect.addEventListener('change', function () {
                    const categoryId = this.value;
                    kpiSelect.innerHTML = '<option value="">Loading...</option>';
                    if (!categoryId) {
                        kpiSelect.innerHTML = '<option value="">Select KPI</option>';
                        return;
                    }

                    fetch(`{{route('account.kpis.index')}}/byCategory/${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            kpiSelect.innerHTML = '<option value="">Select KPI</option>';
                            data.forEach(kpi => {
                                const option = document.createElement('option');
                                option.value = kpi.id;
                                option.textContent = kpi.name;
                                kpiSelect.appendChild(option);
                            });
                        })
                        .catch(() => {
                            kpiSelect.innerHTML = '<option value="">Select KPI</option>';
                        });
                });
            });
        </script>
    </x-slot:scripts>
</x-app-layout>
