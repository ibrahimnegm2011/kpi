<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-key mr-3"></i> KPI Definitions </h1>
            <div class="flex gap-3">
                <a class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('account.kpis.create')}}">Add KPI Definition</a>
            </div>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex items-center justify-items-center gap-3">
                    <input name="filter[title]" onblur="submitForm()" placeholder="Name"
                           value="{{request('filter.title')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">

                    <select name="filter[category]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Categories</option>
                        @foreach(\App\Models\Category::forAccount() as $category)
                            <option value="{{$category->id}}" {{request('filter.category') !== $category->id ? '' : 'selected' }}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="flex gap-3 mt-5">
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.active') === null || request('filter.active') === '' ? 'bg-secondary-100' : '' }}"
                                onclick="setActive('')">
                            All
                        </button>
                        <button type="button"
                                class="px-4 py-2 border-t border-b border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.active') === '1' ? 'bg-secondary-100' : '' }}"
                                onclick="setActive('1')">
                            Active
                        </button>
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 bg-white hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.active') === '0' ? 'bg-secondary-100' : '' }}"
                                onclick="setActive('0')">
                            Inactive
                        </button>
                        <input type="hidden" name="filter[active]" id="filterActive" value="{{ request('filter.active') }}">
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
                        #
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Name
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Category
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Unit
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Status
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light"></th>
                </tr>
                </thead>
                <tbody>
                @if(! $kpis->count())
                    <tr class="hover:bg-gray-100">
                        <td colspan="6" class="py-4 px-6 border-b border-grey-light"> No kpis found. </td>
                    </tr>
                @endif
                @foreach($kpis as $kpi)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light">{{$loop->iteration}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$kpi->title}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$kpi->category->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$kpi->measure_unit->title()}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$kpi->is_active ? 'Active': 'Not Active'}}</td>
                        <td class="py-4 px-6 border-b border-grey-light ">
                            <a class="mx-2 text-secondary-500 hover:text-primary-500" href="{{route('account.kpis.edit', $kpi->id)}}"> <i class="fas fa-edit"></i> </a>
                            <form method="POST" action="{{ route('account.kpis.delete', $kpi) }}" style="display:inline;"
                                  onsubmit="return confirmDelete('{{ addslashes($kpi->title) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-secondary-500 hover:text-primary-500"
                                        title="Delete Kpi">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{$kpis->links()}}
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function submitForm() {
                document.getElementById('search-form').submit()
            }

            function setActive(value) {
                document.getElementById('filterActive').value = value;
                submitForm();
            }

            function confirmDelete(kpiTitle) {
                return confirm(`Are you sure you want to delete kpi "${kpiTitle}"? This action cannot be undone.`);
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
