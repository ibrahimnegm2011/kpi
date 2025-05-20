<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentsController extends Controller
{
    public function index()
    {
        return view('departments.index', [
            'departments' => QueryBuilder::for(Department::class)->allowedFilters([
                AllowedFilter::partial('name'),
            ])->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Department $department = null)
    {
        if ($department && in_array($department->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('departments.form', compact('department'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $data['created_by'] = Auth::id();

        Department::create($data);

        return redirect(route('departments.index'))->with(['success' => 'Department has been created successfully']);
    }

    public function update(Department $department, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $department->update($data);

        return redirect(route('departments.index'))->with(['success' => 'Department has been updated successfully']);
    }

    public function delete(Department $department)
    {
        $department->delete();

        return redirect(route('departments.index'))->with(['success' => 'Department has been deleted successfully.']);
    }
}
