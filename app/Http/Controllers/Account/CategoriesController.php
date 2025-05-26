<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Kpi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('account.categories.index', [
            'categories' => QueryBuilder::for(Category::class)->allowedFilters([
                AllowedFilter::partial('name'),
            ])
                ->where('account_id', Auth::user()->account_id)
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Category $category = null)
    {
        if ($category && in_array($category->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('account.categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string',
                Rule::unique('categories', 'name')->where('account_id', Auth::user()->account_id)
            ],
            'description' => ['nullable', 'string'],
        ]);

        $data['account_id'] = Auth::user()->account_id;
        $data['created_by'] = Auth::id();

        Category::create($data);

        return redirect(route('account.categories.index'))->with(['success' => 'Category has been created successfully']);
    }

    public function update(Category $category, Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string',
                Rule::unique('categories', 'name')->ignoreModel($category)
                    ->where('account_id', Auth::user()->account_id)
            ],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect(route('account.categories.index'))->with(['success' => 'Category has been updated successfully']);
    }

    public function delete(Category $category)
    {
        //TODO: check the model is related to the current account or not
        if(Kpi::where('category_id', $category->id)->count() > 0) {
            return redirect(route('account.categories.index'))->with(['error' => 'Category has kpis. Please delete them first.']);
        }

        $category->delete();

        return redirect(route('account.categories.index'))->with(['success' => 'Category has been deleted successfully.']);
    }
}
