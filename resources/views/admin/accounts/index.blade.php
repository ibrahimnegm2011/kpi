@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-sitemap mr-3"></i> Accounts </h1>
            <div class="flex gap-3">
                <a class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('admin.accounts.create')}}">Add Account</a>
            </div>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex justify-start items-center justify-items-center gap-3">
                    <input name="filter[name]" onblur="submitForm()" placeholder="Account Name"
                           value="{{request('filter.name')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">

                    <input name="filter[contact_name]" onblur="submitForm()" placeholder="Name"
                           value="{{request('filter.contact_name')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">

                    <input name="filter[contact_email]" onblur="submitForm()" placeholder="Email"
                           value="{{request('filter.contact_email')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">
                </div>
                <div class="flex gap-3 mt-5">

                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.active') === null || request('filter.active') === '' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setActive('')">
                            All
                        </button>
                        <button type="button"
                                class="px-4 py-2 border-t border-b border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.active') === '1' ? 'bg-secondary-300 text-white' : '' }}"
                                onclick="setActive('1')">
                            Active
                        </button>
                        <button type="button"
                                class="px-4 py-2 border border-secondary-300 text-secondary-700 hover:bg-secondary-50 focus:z-10 focus:ring-2 focus:ring-secondary-600 {{ request('filter.active') === '0' ? 'bg-secondary-300 text-white' : '' }}"
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
                        Account Name
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Name
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Email
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Phone
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Status
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light"></th>
                </tr>
                </thead>
                <tbody>
                @if(! $accounts->count())
                    <tr class="hover:bg-gray-100 text-center">
                        <td colspan="7" class="py-4 px-6 border-b border-grey-light"> No accounts found. </td>
                    </tr>
                @endif
                @foreach($accounts as $account)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6 border-b border-grey-light">{{$loop->iteration}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$account->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$account->contact_name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$account->contact_email}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$account->contact_phone}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$account->is_active? 'Active': 'Inactive'}}</td>
                        <td class="py-4 px-6 border-b border-grey-light ">
                            <a class="mx-2 text-secondary-500 hover:text-primary-500" href="{{route('admin.accounts.edit', $account->id)}}"> <i class="fas fa-edit"></i> </a>
                            <form method="POST" action="{{ route('admin.accounts.delete', $account) }}" style="display:inline;"
                                  onsubmit="return confirmDelete('{{ addslashes($account->name) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-secondary-500 hover:text-primary-500"
                                        title="Delete Account">
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
            {{$accounts->links()}}
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

            function confirmDelete(accountName) {
                return confirm(`Are you sure you want to delete account "${accountName}"? This action cannot be undone.`);
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
