<?php

namespace App\Http\Controllers\Account;

use App\Enums\MeasureUnit;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Kpi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class KpisController extends Controller
{
    public function index()
    {
        return view('account.kpis.index', [
            'kpis' => QueryBuilder::for(Kpi::class)->allowedFilters([
                AllowedFilter::partial('title'),
                AllowedFilter::exact('category', 'category_id'),
                AllowedFilter::scope('active'),
            ])
                ->where('account_id', Auth::user()->account_id)
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Kpi $kpi = null)
    {
        if ($kpi && in_array($kpi->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('account.kpis.form', compact('kpi'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'measure_unit' => ['required', 'string', Rule::in(MeasureUnit::values())],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $data['account_id'] = Auth::user()->account_id;
        $data['created_by'] = Auth::id();

        Kpi::create($data);

        return redirect(route('account.kpis.index'))->with(['success' => 'Kpi has been created successfully']);
    }

    public function update(Kpi $kpi, Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'measure_unit' => ['required', 'string', Rule::in(MeasureUnit::values())],
            'description' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $kpi->update($data);

        return redirect(route('account.kpis.index'))->with(['success' => 'Kpi has been updated successfully']);
    }


    public function delete(Kpi $kpi)
    {
        if($kpi->forecasts()->count() > 0) {
            abort(403, 'Kpi has forecasts. Please delete them first.');
        }

        $kpi->delete();

        return redirect(route('account.kpis.index'))->with(['success' => 'Kpi has been deleted successfully.']);
    }

    public function byCategory(Category $category)
    {
        return response()->json(
            Kpi::where('category_id', $category->id)
                ->where('account_id', Auth::user()->account_id)
                ->get(['id', 'title'])
        );
    }
}
