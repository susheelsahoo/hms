<?php

namespace Modules\Auth\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Auth\Requests\LoginRequest;
use Modules\Auth\Services\RedirectUserByRole;

class AuthenticatedSessionController
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, RedirectUserByRole $redirector): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (! Auth::attempt([...$credentials, 'status' => 'active'], $remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route($redirector->routeName($user), absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
