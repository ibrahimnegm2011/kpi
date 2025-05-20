<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('categories.index', [
            'categories' => QueryBuilder::for(Category::class)->allowedFilters([
                AllowedFilter::partial('name'),
            ])->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Category $category = null)
    {
        if ($category && in_array($category->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $data['created_by'] = Auth::id();

        Category::create($data);

        return redirect(route('categories.index'))->with(['success' => 'Category has been created successfully']);
    }

    public function update(Category $category, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect(route('categories.index'))->with(['success' => 'Category has been updated successfully']);
    }

    public function delete(Category $category)
    {
        $category->delete();

        return redirect(route('categories.index'))->with(['success' => 'Category has been deleted successfully.']);
    }
}
