<?php

namespace App\Http\Controllers;

use App\Mail\InviteRepresentative;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UsersController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => QueryBuilder::for(User::class)->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('company', 'company_id'),
                AllowedFilter::exact('department', 'department_id'),
                AllowedFilter::scope('active'),
                AllowedFilter::scope('type'),
            ])->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?User $user = null)
    {
        if ($user && in_array($user->id, [Auth::id()])) {
            throw new ModelNotFoundException;
        }

        return view('users.form', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => Password::required(),
            'is_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'permissions' => ['nullable', 'array', 'min:1'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_admin'] = $request->boolean('is_admin');

        $data['created_by'] = Auth::id();

        $user = User::create(Arr::except($data, ['permissions']));

        if ($data['is_admin']) {
            $data['permissions'] = [];
        }

        if ($data['permissions'] ?? false) {
            $user->addPermissions($data['permissions']);
        }

        return redirect(route('users.index'))->with(['success' => 'User has been created successfully']);
    }

    public function update(User $user, Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'name' => ['nullable', 'string'],
            'password' => ['nullable', ...Password::sometimes()],
            'company_id' => ['sometimes', Rule::exists('companies', 'id')],
            'department_id' => ['sometimes', Rule::exists('departments', 'id')],
            'is_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'permissions' => ['sometimes', 'array'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_admin'] = $request->boolean('is_admin');

        $user->update(Arr::except($data, ['permissions']));

        $data['permissions'] = $data['is_admin'] ? [] : ($data['permissions'] ?? []);

        $user->addPermissions($data['permissions']);

        return redirect(route('users.index'))->with(['success' => 'User has been updated successfully']);
    }

    public function invite_form()
    {
        return view('users.invite');
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'company_id' => ['required', Rule::exists('companies', 'id')],
            'department_id' => ['required', Rule::exists('departments', 'id')],
            'position' => ['nullable', 'string'],
        ]);

        $data['is_representative'] = true;

        $data['created_by'] = Auth::id();

        try {
            DB::transaction(function () use ($data) {
                $user = User::create($data);

                Mail::to($user)->sendNow(new InviteRepresentative($user));
            });
        } catch (\Throwable $th) {
            return redirect(route('users.invite'))->with(['error' => 'Something wrong. Try again later.']);
        }

        return redirect(route('users.index'))->with(['success' => 'User has been created and Invitation has been sent.']);

    }

    public function onboarding_form(Request $request, User $user)
    {
        if (! $request->hasValidSignature() || $user->registered_at) {
            abort(401);
        }

        return view('users.onboarding', compact('user'));
    }

    public function onboarding(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'position' => ['nullable', 'string'],
            'password' => [Password::required(), 'confirmed'],
        ]);

        $data['registered_at'] = now();

        $user->update($data);

        Auth::logout();

        return redirect(route('login'))->with(['status' => 'Account has been registered. Please login with your new credentials.']);
    }

    public function delete(User $user)
    {
        $user->delete();

        return redirect(route('users.index'))->with(['success' => 'User has been deleted successfully.']);
    }
}
