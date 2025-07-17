<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    protected function handleUserActivation($email)
    {
        $user = User::with('account', 'agent_assignments.account')->where('email', $email)->first();
        if (! $user->is_active) {
            return 'This user is not active. Contact your administrator.';
        }

        if ($user->type == UserType::ACCOUNT && ! $user->account->is_active) {
            return 'Your account is not active. Contact your administrator.';
        }

        if (
            $user->type == UserType::AGENT &&
            ! $user->agent_assignments
                ->pluck('account.is_active')->unique()
                ->reduce(fn ($carry, $item) => $item || $carry, false)
        ) {
            return 'Your accounts is not active. Contact your administrator.';
        }

        return true;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $activeHandler = $this->handleUserActivation($request->email);

        if ($activeHandler !== true) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => __($activeHandler),
            ]);
        }

        $request->session()->regenerate();

        $intendedPath = parse_url(session()->pull('url.intended', ''), PHP_URL_PATH) ?: '/';

        $user = Auth::user();
        if ($user->type == UserType::ADMIN) {
            if (Str::startsWith($intendedPath, '/admin')) {
                return redirect()->intended(route('home', absolute: false));
            } else {
                return redirect()->route('admin.home');
            }
        }

        if ($user->type == UserType::AGENT) {
            if (Str::startsWith($intendedPath, '/agent')) {
                return redirect()->intended(route('home', absolute: false));
            } else {
                return redirect()->route('agent.home');
            }
        }

        if (! Str::startsWith($intendedPath, 'admin') || ! Str::startsWith($intendedPath, 'agent')) {
            return redirect()->intended(route('home', absolute: false));
        }

        return redirect()->route('account.home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
