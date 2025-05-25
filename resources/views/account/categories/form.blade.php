<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('account.categories.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">{{$category ? 'Update '.($category->name ?? $category->email) : 'Add New Category'}}</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{$category ? route('account.categories.update', $category->id) : route('account.categories.store')}}"
              method="post">
            @csrf
            @method($category ? 'PUT' : 'POST')

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-name">
                        Name*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-name" name="name" type="text" placeholder="Name..."
                        value="{{old('name', $category?->name)}}">
                    @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-description">
                        Description
                    </label>

                    <textarea
                        class="appearance-none bg-transparent border-b block w-full bg-gray-200 text-gray-700  rounded py-3 px-4 leading-tight focus:outline-none  focus:bg-gray-50"
                        id="grid-description" name="description"
                        placeholder="Description...">{{old('description', $category?->description)}}</textarea>
                    @error('description') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror

                </div>
            </div>
            <div class="md:flex md:items-center">
                <button
                    class="m-auto shadow bg-emerald-400 hover:bg-emerald-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                    type="submit">
                    {{$category ? 'Save' : 'Create'}}
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
