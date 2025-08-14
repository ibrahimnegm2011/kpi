<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AgentAssignment;
use App\Models\Department;
use App\Models\Forecast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentsController extends Controller
{
    public function index()
    {
        return view('account.departments.index', [
            'departments' => QueryBuilder::for(Department::class)->allowedFilters([
                AllowedFilter::partial('name'),
            ])
                ->where('account_id', Auth::user()->account_id)
                ->latest()
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Department $department = null)
    {
        if ($department && in_array($department->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('account.departments.form', compact('department'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string',
                Rule::unique('departments', 'name')
                    ->where('account_id', Auth::user()->account_id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $data['account_id'] = Auth::user()->account_id;
        $data['created_by'] = Auth::id();

        Department::create($data);

        return redirect(route('account.departments.index'))->with(['success' => 'Department has been created successfully']);
    }

    public function update(Department $department, Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string',
                Rule::unique('departments', 'name')->ignoreModel($department)
                    ->where('account_id', Auth::user()->account_id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $department->update($data);

        return redirect(route('account.departments.index'))->with(['success' => 'Department has been updated successfully']);
    }

    public function delete(Department $department)
    {
        if (AgentAssignment::where('department_id', $department->id)->count() > 0) {
            return redirect(route('account.departments.index'))->with(['error' => 'Department has assigned to agents. Please delete them first.']);
        }

        if (Forecast::where('department_id', $department->id)->count() > 0) {
            return redirect(route('account.departments.index'))->with(['error' => 'Department has forecasts. Please delete them first.']);
        }
        $department->delete();

        return redirect(route('account.departments.index'))->with(['success' => 'Department has been deleted successfully.']);
    }
}
