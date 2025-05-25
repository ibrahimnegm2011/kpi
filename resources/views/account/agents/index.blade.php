@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="w-full mt-1">
        <div class="flex justify-between mb-5">
            <h1 class="text-3xl text-black"><i class="fas fa-user-tie mr-3"></i> Agents </h1>
            <div class="flex gap-3">
                <a class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded"
                   href="{{route('account.agents.create')}}">Invite Agent</a>

            </div>
        </div>

        <div class="bg-white mt-5 p-4">
            <form method="get" id="search-form">
                <div class="flex justify-around items-center justify-items-center gap-3">
                    <input name="filter[name]" onblur="submitForm()" placeholder="Name"
                           value="{{request('filter.name')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">

                    <input name="filter[email]" onblur="submitForm()" placeholder="Email"
                           value="{{request('filter.email')}}"
                           class="bg-transparent border-b max-w-xs w-full text-gray-700 py-3 px-4 leading-tight focus:outline-none focus:bg-gray-50">

                    <select name="filter[company]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Companies</option>
                        @foreach(\App\Models\Company::forAccount() as $company)
                            <option value="{{$company->id}}" {{request('filter.company') !== $company->id ? '' : 'selected' }}>
                                {{$company->name}}
                            </option>
                        @endforeach
                    </select>
                    <select name="filter[department]" class="max-w-xs w-full" onchange="submitForm()">
                        <option selected value="">All Departments</option>
                        @foreach(\App\Models\Department::forAccount() as $department)
                            <option value="{{$department->id}}" {{request('filter.department') !== $department->id ? '' : 'selected' }}>
                                {{$department->name}}
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
                        Email
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Company - Department
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                        Status
                    </th>
                    <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light"></th>
                </tr>
                </thead>
                <tbody>
                @if(! $users->count())
                    <tr class="hover:bg-gray-100">
                        <td colspan="7" class="py-4 px-6 border-b border-grey-light"> No users found. </td>
                    </tr>
                @endif
                @foreach($users as $user)
                    <tr class="hover:bg-gray-100 {{$user->is_admin ? 'bg-primary-50': ''}}" title="Admin User">
                        <td class="py-4 px-6 border-b border-grey-light">{{$loop->iteration}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$user->name}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$user->email}}</td>
                        <td class="py-4 px-6 border-b border-grey-light">
                            {{$user->agent_assignments?->first()->company?->name}} - {{$user->agent_assignments?->first()->department?->name}}
                            @if($user->agent_assignments->count() > 1)
                                <sub><strong><i>(+{{$user->agent_assignments->count()-1}} more)</i></strong></sub>
                            @endif
                        </td>
                        <td class="py-4 px-6 border-b border-grey-light">{{$user->is_active? 'Active': 'Inactive'}}</td>
                        <td class="py-4 px-6 border-b border-grey-light ">
                            @if(Auth::id() != $user->id)
                                <a class="mx-2 text-secondary-500 hover:text-primary-500" href="{{route('account.agents.edit', $user->id)}}"> <i class="fas fa-edit"></i> </a>
                                <form method="POST" action="{{ route('account.agents.delete', $user) }}" style="display:inline;"
                                      onsubmit="return confirmDelete('{{ addslashes($user->name) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-secondary-500 hover:text-primary-500"
                                            title="Delete User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{$users->links()}}
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

            function confirmDelete(userName) {
                return confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`);
            }
        </script>
    </x-slot:scripts>
</x-app-layout>
