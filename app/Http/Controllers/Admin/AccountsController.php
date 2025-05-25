<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Mail\InviteAccountAdmin;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AccountsController extends Controller
{
    public function index()
    {
        return view('admin.accounts.index', [
            'accounts' => QueryBuilder::for(Account::class)->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('contact_name'),
                AllowedFilter::partial('contact_email'),
            ])->paginate(10)->withQueryString(),
        ]);
    }

    public function form(?Account $account = null)
    {
        return view('admin.accounts.form', compact('account'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('accounts', 'name')],
            'contact_name' => ['required', 'string'],
            'contact_email' => [
                'required', 'email',
                Rule::unique('accounts', 'contact_email'),
                Rule::unique('users', 'email')->whereNotNull('account_id'),
            ],
            'contact_phone' => ['required'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $data['created_by'] = Auth::id();

        DB::transaction(function () use ($data) {
            try {
                $account = Account::create($data);

                $user = $account->users()->create([
                    'name' => $data['contact_name'],
                    'email' => $data['contact_email'],
                    'type' => UserType::ACCOUNT(),
                ]);

                $account->update(['admin_user_id' => $user->id]);

                Mail::to($user)->sendNow(new InviteAccountAdmin($user));
            } catch (\Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
        });

        return redirect(route('admin.accounts.index'))->with(['success' => 'Account has been created successfully']);
    }

    public function update(Account $account, Request $request)
    {
        $account->load('user');

        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('accounts', 'name')->ignore($account)],
            'contact_name' => ['required', 'string'],
            'contact_email' => [
                'required', 'email',
                Rule::unique('accounts', 'contact_email')->ignore($account),
                Rule::unique('users', 'email')->whereNotNull('account_id')->ignore($account->user),
            ],
            'contact_phone' => ['required'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $account->update($data);

        return redirect(route('admin.accounts.index'))->with(['success' => 'Account has been updated successfully']);
    }

    public function delete(Account $account)
    {
        foreach (['companies', 'departments'] as $relation) {
            if ($account->$relation()->count() > 0) {
                abort(403, 'Account has ' . $relation . '. Please delete them first.');
            }
        }

        $account->delete();
        $account->users()->delete();

        return redirect(route('admin.accounts.index'))->with(['success' => 'Account has been deleted successfully.']);
    }
}
