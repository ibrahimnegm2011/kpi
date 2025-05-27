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

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (! User::where('email', $request->email)->first()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => __('This user is not active. Contact your administrator.'),
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
