<?php

namespace App\Http\Controllers\Agent;

use App\Enums\Permission;
use App\Enums\UserType;
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

class OnboardingController extends Controller
{
    public function form(Request $request, ?User $user = null)
    {
        if (! $request->hasValidSignature() || $user->type != UserType::AGENT || $user->onboarded_at) {
            abort(401);
        }

        $user->loadMissing('agent_assignments.account');
        $accountsList = $user->agent_assignments
            ->map(fn ($assignment) => $assignment->account->name)
            ->unique()->values();

        return view('agent.onboarding.form', compact('user', 'accountsList'));
    }

    public function onboarding(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'password' => [Password::required(), 'confirmed'],
        ]);

        $data['onboarded_at'] = now();

        $user->update($data);

        Auth::logout();

        return redirect(route('login'))->with(['status' => 'Agent User has been registered. Please login with your new credentials.']);
    }
}
