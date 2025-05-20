<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('users.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">{{$user ? 'Update '.($user->name ?? $user->email) : 'Add New User'}}</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{$user ? route('users.update', $user->id) : route('users.store')}}"
              method="post">
            @csrf
            @method($user ? 'PUT' : 'POST')

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-name">
                        Name*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-name" name="name" type="text" placeholder="Name..."
                        value="{{old('name', $user?->name)}}">
                    @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-email">
                        Email*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-email" name="email" type="email" placeholder="example@sirc.sa"
                        value="{{old('email', $user?->email)}}" required>
                    @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                @if(!$user || !$user->is_representative)
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="grid-password">
                            Password*
                        </label>
                        <input
                            class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                            id="grid-password" name="password" type="password">
                        @error('password') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="w-full md:w-1/2 flex justify-center items-center pt-3">
                        <div class="flex items-center dark:border-gray-700">
                            <input id="admin-checkbox" type="checkbox" value="1" name="is_admin" onclick="adminChecked()"
                                   {{$user?->is_admin || old('is_admin', $user?->is_admin) ? 'checked' : '' }}
                                   class="w-7 h-7 text-emerald-400 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="admin-checkbox"
                                   class="w-full py-4 ms-2 font-bold text-gray-900 dark:text-gray-300">Admin</label>
                        </div>
                    </div>

                    <div id="permissions-div" class="w-full mb-5 {{ $user?->is_admin || old('is_admin', $user?->is_admin) ? 'hidden' : ''}}">
                        <label class="w-full py-4 ms-2 font-bold text-gray-900 dark:text-gray-300">Permissions: </label>
                        <div class="border rounded-md px-4 grid grid-cols-4 justify-start items-start content-start align-middle justify-items-start">
                        @foreach(\App\Enums\Permission::cases() as $i => $case)
                            <div class="w-full md:w-1/2  pt-3">
                                <div class="flex items-center dark:border-gray-700">
                                    <input id="permission-check-{{$i}}" type="checkbox" value="{{$case->value}}" name="permissions[]"
                                           {{in_array($case->value, old('permissions', [])) || ($user?->hasNonAdminPermission($case->value)) ? 'checked' : '' }}
                                           class="w-7 h-7 text-emerald-400 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="permission-check-{{$i}}"
                                           class="w-full py-4 ms-2 font-bold text-gray-900 dark:text-gray-300">{{\Illuminate\Support\Str::headline($case->value)}}</label>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
               @endif

                @if($user?->is_representative)
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="grid-company">
                            Representative Company*
                        </label>
                        <select id="grid-company" name="company_id" class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                            <option {{old('company_id', $user?->company_id) ? '' : 'selected'}} disabled>Select Company</option>
                            @foreach(\App\Models\Company::all() as $company)
                                <option value="{{$company->id}}"{{old('company_id', $user?->company_id) == $company->id ? 'selected' : '' }}>
                                    {{$company->name}}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                               for="grid-department">
                            Representative Department*
                        </label>
                        <select id="grid-department" name="department_id" class="appearance-none bg-transparent border block w-full text-gray-700 w-3/12 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                            <option {{old('department_id', $user?->department_id) ? '' : 'selected'}} disabled>Select Company</option>
                            @foreach(\App\Models\Department::all() as $department)
                                <option value="{{$department->id}}"{{old('department_id', $user?->department_id) == $department->id ? 'selected' : '' }}>
                                    {{$department->name}}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>
                @endif


                <div class="w-full flex justify-center items-center pt-3">
                    <div class="flex items-center dark:border-gray-700">
                        <input id="active-checkbox" type="checkbox" value="1" name="is_active"
                               {{!$user || old('is_active', $user->is_active) ? 'checked' : '' }}
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
                    {{$user ? 'Save' : 'Create'}}
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
