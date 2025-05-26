<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('account.kpis.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">{{$kpi ? 'Update '.($kpi->name) : 'Add New KPI'}}</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{$kpi ? route('account.kpis.update', $kpi->id) : route('account.kpis.store')}}"
              method="post">
            @csrf
            @method($kpi ? 'PUT' : 'POST')

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-name">
                        Name*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-name" name="name" type="text" placeholder="Name..."
                        value="{{old('name', $kpi?->name)}}">
                    @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-category">
                        Category*
                    </label>
                    <select id="grid-category" name="category_id" class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('category_id', $kpi?->category_id) ? '' : 'selected'}} disabled>Select Category</option>
                        @foreach(\App\Models\Category::forAccount() as $category)
                            <option value="{{$category->id}}"{{old('category_id', $kpi?->category_id) == $category->id ? 'selected' : '' }}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-definition">
                        Definition
                    </label>

                    <textarea
                        class="appearance-none bg-transparent border-b block w-full bg-gray-200 text-gray-700  rounded mb-3 py-3 px-4 leading-tight focus:outline-none  focus:bg-gray-50"
                        id="grid-definition" name="definition"
                        placeholder="Definition...">{{old('definition', $kpi?->definition)}}</textarea>
                    @error('definition') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-equation">
                        Equation
                    </label>

                    <textarea
                        class="appearance-none bg-transparent border-b block w-full bg-gray-200 text-gray-700  rounded mb-3 py-3 px-4 leading-tight focus:outline-none  focus:bg-gray-50"
                        id="grid-equation" name="equation"
                        placeholder="Equation...">{{old('equation', $kpi?->equation)}}</textarea>
                    @error('equation') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-measure_unit">
                        Unit of Measurement (UOM)
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-measure_unit" name="unit_of_measurement" type="text" placeholder="Unit of Measurement..."
                        value="{{old('unit_of_measurement', $kpi?->unit_of_measurement)}}">
                    @error('unit_of_measurement') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-symbol">
                        UOM Symbol
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-symbol" name="symbol" type="text" placeholder="Symbol..."
                        value="{{old('symbol', $kpi?->symbol)}}">
                    @error('symbol') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full flex justify-center items-center pt-3">
                    <div class="flex items-center dark:border-gray-700">
                        <input id="active-checkbox" type="checkbox" value="1" name="is_active"
                               {{!$kpi || old('is_active', $kpi->is_active) ? 'checked' : '' }}
                               class="w-7 h-7 text-emerald-400 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="active-checkbox"
                               class="w-full py-4 ms-2 font-bold text-gray-900 dark:text-gray-300">Active</label>
                    </div>
                </div>
            </div>
            <div class="md:flex md:items-center">
                <button
                    class="m-auto shadow bg-emerald-400 hover:bg-emerald-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                    type="submit">
                    {{$kpi ? 'Save' : 'Create'}}
                </button>
            </div>
        </form>
    </div>


    <x-slot:scripts>
        <script>
            function adminChecked() {
                if(document.getElementById('admin-checkbox').checked) {
                    document.getElementById('permissions-div').classList.add('hidden')
                } else {
                    console.log(document.getElementById('permissions-div'))
                    document.getElementById('permissions-div').classList.remove('hidden')
                }
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
