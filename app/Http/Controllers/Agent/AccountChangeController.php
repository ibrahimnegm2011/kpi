<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountChangeController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'accountId' => [
                'required', 'string',
                Rule::exists('accounts', 'id'),
            ],
        ]);

        session(['selected_account' => $request->accountId]);

        return redirect()->route('agent.home');
    }
}
