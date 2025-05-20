<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CompaniesController extends Controller
{
    public function index()
    {
        return view('companies.index', [
            'companies' => QueryBuilder::for(Company::class)->allowedFilters([
                AllowedFilter::partial('name'),
            ])->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Company $company = null)
    {
        if ($company && in_array($company->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('companies.form', compact('company'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $data['created_by'] = Auth::id();

        Company::create($data);

        return redirect(route('companies.index'))->with(['success' => 'Company has been created successfully']);
    }

    public function update(Company $company, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $company->update($data);

        return redirect(route('companies.index'))->with(['success' => 'Company has been updated successfully']);
    }

    public function delete(Company $company)
    {
        $company->delete();

        return redirect(route('companies.index'))->with(['success' => 'Company has been deleted successfully.']);
    }
}
