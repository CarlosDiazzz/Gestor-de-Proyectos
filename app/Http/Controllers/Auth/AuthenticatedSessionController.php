<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    /**
     * Maneja la solicitud de autenticación entrante y redirige al usuario según su rol (Admin, Juez, Participante).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user->roles->contains('nombre', 'Admin')) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->roles->contains('nombre', 'Juez')) {
            return redirect()->intended(route('juez.dashboard'));
        } elseif ($user->roles->contains('nombre', 'Participante')) {
            if (!$user->participante) {
                return redirect()->intended(route('participante.registro.inicial'));
            }
            return redirect()->intended(route('participante.dashboard'));
        }

        return redirect('/');
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
