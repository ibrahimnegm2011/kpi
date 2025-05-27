<?php

namespace App\Http\Controllers\Account;

use App\Enums\Permission;
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
        if (! $request->hasValidSignature() || $user->type != UserType::ACCOUNT || $user->onboarded_at) {
            abort(401);
        }

        $user->loadMissing('account');

        return view('account.onboarding.form', compact('user'));
    }

    public function onboarding(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'password' => [Password::required(), 'confirmed'],
        ]);

        $data['onboarded_at'] = now();

        $user->update($data);

        $permissions = Permission::accountPermissions();
        if (! empty($permissions)) {
            $permissionsData = collect($permissions)->map(fn (Permission $permissionCase) => [
                'permission' => $permissionCase->value,
            ])->all();

            $user->permissions()->createMany($permissionsData);
        }

        Auth::logout();

        return redirect(route('login'))->with(['status' => 'Account Admin has been registered. Please login with your new credentials.']);
    }
}
