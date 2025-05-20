<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('users.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">Invite User</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{route('users.send_invitation')}}"
              method="post">
            @csrf

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-email">
                        Email*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-email" name="email" type="email" placeholder="example@sirc.sa"
                        value="{{old('email')}}" required>
                    @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-name">
                        Name*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-name" name="name" type="text" placeholder="Name..."
                        value="{{old('name')}}">
                    @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-company">
                        Representative Company*
                    </label>
                    <select id="grid-company" name="company_id"
                            class="appearance-none bg-transparent border block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('company_id') ? '' : 'selected'}} disabled>Select Company</option>
                        @foreach(\App\Models\Company::all() as $company)
                            <option value="{{$company->id}}"{{old('company_id') == $company->id ? 'selected' : '' }}>
                                {{$company->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-department">
                        Representative Company*
                    </label>
                    <select id="grid-department" name="department_id"
                            class="appearance-none bg-transparent border block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option {{old('department_id') ? '' : 'selected'}} disabled>Select Department</option>
                        @foreach(\App\Models\Department::all() as $department)
                            <option value="{{$department->id}}" {{old('department_id') == $department->id ? 'selected' : '' }}>
                                {{$department->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-position">
                        Position
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-position" name="position" type="text" placeholder="Position..."
                        value="{{old('position')}}">
                    @error('position') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

            </div>
            <div class="md:flex md:items-center">
                <button
                    class="m-auto shadow bg-emerald-400 hover:bg-emerald-700 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                    type="submit">
                    Send
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
