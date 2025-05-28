<?php

namespace App\Http\Controllers\Agent;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class OnboardingController extends Controller
{
    public function form(Request $request, ?User $user = null)
    {
        if (! $request->hasValidSignature() || $user->type != UserType::AGENT) {
            abort(401);
        }

        if($user->onboarded_at) {
            return redirect(route('login'))->with(['status' => 'This email has already registered. Please login with your credentials.']);
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
