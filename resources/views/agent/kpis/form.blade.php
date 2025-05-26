<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="#" onclick="history.back();"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">Submit "{{$forecast->kpi->name}}"</h1>
    </div>

    <div class="overflow-auto mt-5">
        @php
            $fields = [
                'KPI' => $forecast->kpi->name,
                'Category' => $forecast->kpi->category->name,
                'Month' => \Carbon\Carbon::create()->month($forecast->month)->year($forecast->year)->format('F, Y'),
                'Definition' => $forecast->kpi->definition,
                'Equation' => $forecast->kpi->equation,
                'Unit of Measurement' => $forecast->kpi->unit_of_measurement,
                'Target' => "$forecast->target " . ($forecast->kpi->symbol ?? ''),
            ];
            $i = 0;
        @endphp
        <div class="grid grid-cols-2 gap-y-2 gap-1">
            @foreach($fields as $key => $value)
                <div class="grid grid-cols-2 p-3 bg-[#7473b6] @if($i%4 > 1) bg-opacity-10 @else bg-opacity-20 @endif">
                    <div class="font-bold">{{$key}}</div>
                    <div>{!! $value !!}</div>
                </div>
                @php $i++; @endphp
            @endforeach
        </div>
    </div>

    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full" enctype="multipart/form-data"
              action="{{route('agent.kpi_submit', $forecast)}}"
              method="post">
            @csrf

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-value">
                        Value*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-value" name="value" type="text" placeholder="Value..." required
                        value="{{old('value', $forecast->value)}}">
                    @error('value') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

{{--                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">--}}
{{--                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"--}}
{{--                           for="grid-evidence">--}}
{{--                        Evidence <sub>(zip,doc,pdf)</sub>*--}}
{{--                    </label>--}}
{{--                    <input--}}
{{--                        class="appearance-none bg-transparent block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"--}}
{{--                        id="grid-evidence" name="evidence_filepath" type="file"--}}
{{--                        onchange="if(this.files[0] && this.files[0].size > 10485760){ alert('File must be less than 10 MB'); this.value = ''; }"--}}
{{--                        accept="image/jpeg,image/png,--}}
{{--                        application/zip,--}}
{{--                        application/pdf,--}}
{{--                        application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">--}}
{{--                    @error('evidence_filepath') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror--}}
{{--                </div>--}}

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-remarks">
                        Remarks
                    </label>
                    <textarea
                        class="appearance-none bg-transparent border-b block w-full bg-gray-200 text-gray-700  rounded py-3 px-4 leading-tight focus:outline-none  focus:bg-gray-50"
                        id="grid-remarks" name="remarks"
                        placeholder="Remarks...">{{old('remarks', $forecast->remarks)}}</textarea>
                    @error('remarks') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex flex-wrap -mx-3 mb-6">

            </div>

            <div class="md:flex md:items-center">
                <button
                    class="m-auto shadow bg-emerald-400 hover:bg-emerald-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                    type="submit">
                    Submit
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
