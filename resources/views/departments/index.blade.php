<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-layer-group mr-3"></i> Departments </h1>
            <div class="flex gap-3">
                <a class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('departments.create')}}">Add Department</a>
            </div>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex items-center justify-items-center gap-3">
                    <input name="filter[name]" onblur="submitForm()" placeholder="Name"
                           value="{{request('filter.name')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">
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
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light"></th>
                </tr>
                </thead>
                <tbody>
                @if(! $departments->count())
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light" colspan="3"> No categories found. </td>
                    </tr>
                @endif
                @foreach($departments as $department)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light">{{$loop->iteration}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$department->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light ">
                            <a class="mx-2 text-secondary-500 hover:text-primary-500" href="{{route('departments.edit', $department->id)}}"> <i class="fas fa-edit"></i> </a>
                            <form method="POST" action="{{ route('departments.delete', $department) }}" style="display:inline;"
                                  onsubmit="return confirmDelete('{{ addslashes($department->name) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-secondary-500 hover:text-primary-500"
                                        title="Delete Department">
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
            {{$departments->links()}}
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function submitForm() {
                document.getElementById('search-form').submit()
            }

            function confirmDelete(departmentName) {
                return confirm(`Are you sure you want to delete department "${departmentName}"? This action cannot be undone.`);
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
