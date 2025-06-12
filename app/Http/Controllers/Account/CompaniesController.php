<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\AgentAssignment;
use App\Models\Company;
use App\Models\Forecast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CompaniesController extends Controller
{
    public function index()
    {
        return view('account.companies.index', [
            'companies' => QueryBuilder::for(Company::class)->allowedFilters([
                AllowedFilter::partial('name'),
            ])
                ->where('account_id', Auth::user()->account_id)
                ->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Company $company = null)
    {
        if ($company && in_array($company->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('account.companies.form', compact('company'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string',
                Rule::unique('companies', 'name')->where('account_id', Auth::user()->account_id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $data['account_id'] = Auth::user()->account_id;
        $data['created_by'] = Auth::id();

        Company::create($data);

        return redirect(route('account.companies.index'))->with(['success' => 'Company has been created successfully']);
    }

    public function update(Company $company, Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string',
                Rule::unique('companies', 'name')->ignoreModel($company)
                    ->where('account_id', Auth::user()->account_id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $company->update($data);

        return redirect(route('account.companies.index'))->with(['success' => 'Company has been updated successfully']);
    }

    public function delete(Company $company)
    {
        if (AgentAssignment::where('company_id', $company->id)->count() > 0) {
            return redirect(route('account.companies.index'))->with(['error' => 'Company has assigned to agents. Please delete them first.']);
        }

        if (Forecast::where('company_id', $company->id)->count() > 0) {
            return redirect(route('account.companies.index'))->with(['error' => 'Company has forecasts. Please delete them first.']);
        }

        $company->delete();

        return redirect(route('account.companies.index'))->with(['success' => 'Company has been deleted successfully.']);
    }
}
