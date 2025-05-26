<?php

namespace App\Http\Controllers\Agent;

use App\Enums\Permission;
use App\Enums\UserType;
use App\Http\Controllers\Account\AgentsController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AccountChangeController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'accountId' => [
                'required', 'string',
                Rule::exists('accounts', 'id')
            ]
        ]);

        session(['selected_account' => $request->accountId]);

        return back()->with(['success' => 'Account changed']);
    }
}
