<?php

namespace App\Http\Controllers\Account;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UsersController extends Controller
{
    public function index()
    {
        return view('account.users.index', [
            'users' => QueryBuilder::for(User::class)->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::scope('active'),
            ])
                ->where('account_id', Auth::user()->account_id)
                ->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?User $user = null)
    {
        if ($user) {

            if ($user->type != UserType::ACCOUNT || $user->account_id != Auth::user()->account_id) {
                throw new ModelNotFoundException;
            }

            $user->loadMissing('permissions');
        }

        return view('account.users.form', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => Password::required(),
            'is_active' => ['sometimes', 'boolean'],
            'permissions' => ['nullable', 'array', 'min:1'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $data['account_id'] = Auth::user()->account_id;
        $data['type'] = UserType::ACCOUNT();
        $data['created_by'] = Auth::id();

        $user = User::create(Arr::except($data, ['permissions']));

        if ($data['permissions'] ?? false) {
            $user->addPermissions($data['permissions']);
        }

        return redirect(route('account.users.index'))->with(['success' => 'User has been created successfully']);
    }

    public function update(User $user, Request $request)
    {
        if ($user->type != UserType::ACCOUNT || $user->account_id != Auth::user()->account_id) {
            throw new ModelNotFoundException;
        }

        $data = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'name' => ['required', 'string'],
            'password' => ['nullable', ...Password::sometimes()],
            'is_active' => ['sometimes', 'boolean'],
            'permissions' => ['sometimes', 'array'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $user->update(Arr::except($data, ['permissions']));

        $data['permissions'] = $data['permissions'] ?? [];

        $user->addPermissions($data['permissions']);

        return redirect(route('account.users.index'))->with(['success' => 'User has been updated successfully']);
    }

    public function delete(User $user)
    {
        if ($user->id == Auth::id()) {
            return redirect(route('account.users.index'))->with(['error' => 'You can not delete yourself.']);
        }

        if ($user->type != UserType::ACCOUNT || $user->account_id != Auth::user()->account_id) {
            throw new ModelNotFoundException;
        }

        $user->delete();

        return redirect(route('account.users.index'))->with(['success' => 'User has been deleted successfully.']);
    }
}
