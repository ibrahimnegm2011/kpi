<x-app-layout xmlns:x-slot="http://www.w3.org/1999/html">
    <div class="inline-flex items-center">
        <a class="mr-3" href="{{route('account.agents.index')}}"><i class="fas fa-arrow-left"></i> </a>
        <h1 class=" text-3xl text-black">{{$user ? 'Update '.($user->name ?? $user->email) : 'Invite Agent'}}</h1>
    </div>


    <div class="w-full mt-6 rounded overflow-hidden shadow-md bg-white p-10">
        <form class="w-full"
              action="{{$user ? route('account.agents.update', $user->id) : route('account.agents.store')}}"
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
                        value="{{old('name', $user?->name)}}" required {{$user ? 'disabled' : ''}}>
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
                        value="{{old('email', $user?->email)}}" required  {{$user ? 'disabled' : ''}}>
                    @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                {{-- Company Selector --}}
                <div class="w-full md:w-5/12 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="company_selector">
                        Select Company
                    </label>
                    <select id="company_selector" class="appearance-none bg-transparent border block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option value="">Select Company</option>
                        @foreach(\App\Models\Company::forAccount() as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Department Selector --}}
                <div class="w-full md:w-5/12 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                           for="department_selector">
                        Select Department
                    </label>
                    <select id="department_selector" class="appearance-none bg-transparent border block w-full text-gray-700 py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-gray-50">
                        <option value="">Select Department</option>
                        {{-- Consider populating this dynamically based on selected company --}}
                        @foreach(\App\Models\Department::forAccount() as $department)
                            <option value="{{$department->id}}">{{$department->name}}</option>
                        @endforeach
                    </select>
                    <p id="assignment-error" class="text-red-500 text-xs italic mt-1"></p>
                </div>

                {{-- "Add" Button --}}
                <div class="w-full md:w-2/12 px-5 mb-6 md:mb-0 pl-4">
                    <button type="button" id="add_assignment_button" class="bg-secondary-500 hover:bg-secondary-700 text-white font-bold py-3 px-4 my-5 rounded">
                        Assign
                    </button>
                </div>

                {{-- Display Area for Selected Assignments --}}
                <div class="w-full px-3 mb-6">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Assigned Company-Department Pairs
                    </label>
                    <div id="selected_assignments_container" class="w-full md:w-1/2 lg:1/4 mt-2"></div>
                    @error('assignments') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                </div>

                {{-- Hidden input to store the assignments --}}
                <input type="hidden" name="assignments" id="assignments_hidden_input" value="{!! json_encode(Arr::only($assignments, ['companyId', 'departmentId'])) !!}">



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
            document.addEventListener('DOMContentLoaded', function () {
                const companySelector = document.getElementById('company_selector');
                const departmentSelector = document.getElementById('department_selector');
                const addButton = document.getElementById('add_assignment_button');
                const assignmentsContainer = document.getElementById('selected_assignments_container');
                const assignmentsHiddenInput = document.getElementById('assignments_hidden_input');
                const departmentError = document.getElementById('assignment-error');

                let selectedAssignments = [];

                const initialAssignments = JSON.parse('{!! json_encode($assignments ?? '[]') !!}');
                if (initialAssignments.length) {
                    selectedAssignments = initialAssignments;
                    renderAssignments();
                }

                addButton.addEventListener('click', function () {
                    const companyId = companySelector.value;
                    const companyName = companySelector.options[companySelector.selectedIndex]?.text;
                    const departmentId = departmentSelector.value;
                    const departmentName = departmentSelector.options[departmentSelector.selectedIndex]?.text;
                    departmentError.textContent = ''; // clear previous errors


                    if (!companyId || !departmentId) {
                        departmentError.textContent = 'Please select both a company and a department.';
                        return;
                    }

                    // Optional: Check for duplicates
                    const alreadyExists = selectedAssignments.some(
                        assign => assign.companyId === companyId && assign.departmentId === departmentId
                    );
                    if (alreadyExists) {
                        departmentError.textContent = 'This company-department pair is already assigned.';
                        return;
                    }

                    selectedAssignments.push({ companyId, companyName, departmentId, departmentName });
                    renderAssignments();

                    // Reset selectors
                    companySelector.value = '';
                    departmentSelector.value = '';
                    departmentError.textContent = '';
                });

                assignmentsContainer.addEventListener('click', function (event) {
                    if (event.target.classList.contains('remove-assignment')) {
                        const companyIdToRemove = event.target.dataset.companyId;
                        const departmentIdToRemove = event.target.dataset.departmentId;

                        selectedAssignments = selectedAssignments.filter(
                            assign => !(assign.companyId === companyIdToRemove && assign.departmentId === departmentIdToRemove)
                        );
                        renderAssignments();
                    }
                });

                function renderAssignments() {
                    assignmentsContainer.innerHTML = '';
                    selectedAssignments.forEach(assign => {
                        const div = document.createElement('div');
                        div.className = 'flex justify-between items-center p-2 border-b';
                        div.innerHTML = `
                                <span>${assign.companyName} - ${assign.departmentName}</span>
                                    <button type="button"
                                            class="remove-assignment text-red-500 hover:text-red-700 text-sm"
                                            data-company-id="${assign.companyId}"
                                            data-department-id="${assign.departmentId}">
                                        Remove
                                    </button>
                                `;
                        assignmentsContainer.appendChild(div);
                    });
                    // Only output company_id and department_id in the hidden input
                    assignmentsHiddenInput.value = JSON.stringify(selectedAssignments.map(a => ({
                        company_id: a.companyId,
                        department_id: a.departmentId
                    })));
                }

                companySelector.addEventListener('change', function() {
                    departmentError.textContent = '';
                });
                departmentSelector.addEventListener('change', function() {
                    departmentError.textContent = '';
                });
            });


        </script>
    </x-slot:scripts>
</x-app-layout>
