<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('admin.accounts.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">{{$account ? 'Update '.($account->name ?? $account->email) : 'Add New Account'}}</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{$account ? route('admin.accounts.update', $account->id) : route('admin.accounts.store')}}"
              method="post">
            @csrf
            @method($account ? 'PUT' : 'POST')

            @if($errors->isNotEmpty())
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            @endif

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-name">
                        Account Name*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-name" name="name" type="text" placeholder="Name..."
                        value="{{old('name', $account?->name)}}">
                    @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-contact_name">
                        Owner Name*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-contact_name" name="contact_name" type="text" placeholder="Owner Name..."
                        value="{{old('contact_name', $account?->contact_name)}}" required>
                    @error('contact_name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-contact_email">
                        Owner Email*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-contact_email" name="contact_email" type="email" placeholder="example@sirc.sa"
                        value="{{old('contact_email', $account?->contact_email)}}" required>
                    @error('contact_email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="grid-contact_phone">
                        Phone*
                    </label>
                    <input
                        class="appearance-none bg-transparent border-b block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50"
                        id="grid-contact_phone" name="contact_phone" type="text" placeholder="+9665####"
                        value="{{old('contact_phone', $account?->contact_phone)}}" required>
                    @error('contact_phone') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                <div class="w-full flex justify-center items-center pt-3">
                    <div class="flex items-center dark:border-gray-700">
                        <input id="active-checkbox" type="checkbox" value="1" name="is_active"
                               {{!$account || old('is_active', $account->is_active) ? 'checked' : '' }}
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
                    {{$account ? 'Save' : 'Create'}}
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
