<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Carrera;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $carreras = Carrera::all(); // Necesario para el dropdown de participante

        return view('profile.edit', [
            'user' => $user,
            'carreras' => $carreras,
            'esParticipante' => $user->roles->contains('nombre', 'Participante'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $esParticipante = $user->roles->contains('nombre', 'Participante');

        // 1. Validación Dinámica
        if ($esParticipante) {
            // Usamos validación manual o inyectamos el request correspondiente
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
                'telefono' => ['nullable', 'string', 'max:20'],
                'no_control' => ['required', 'string', 'max:20', 'unique:participantes,no_control,'.($user->participante->id ?? 'NULL')],
                'carrera_id' => ['required', 'exists:carreras,id'],
            ]);
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            ]);
        }

        // 2. Actualizar Tabla User
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // 3. Actualizar Tabla Participante (Si aplica)
        if ($esParticipante && $user->participante) {
            $user->participante->update([
                'no_control' => $validated['no_control'],
                'carrera_id' => $validated['carrera_id'],
                // 'telefono' => $validated['telefono'] // Asumiendo que agregaste esta columna a participantes o users
            ]);
            
            // Si el teléfono está en la tabla users, agrégalo al fill de arriba.
            // Si está en participantes, úsalo aquí.
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();
        
        // Eliminar avatar anterior si existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Guardar nuevo avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);
        
        return back()->with('status', 'avatar-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}