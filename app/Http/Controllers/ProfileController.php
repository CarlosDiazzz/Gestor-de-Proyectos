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
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:1024'], // Max 1MB
        ]);

        $user = $request->user();
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = $user->id . '.jpg';
            
            // Asegurar que el directorio existe
            if (!Storage::disk('public')->exists('avatars')) {
                Storage::disk('public')->makeDirectory('avatars');
            }

            // Guardar imagen (sobrescribir si existe)
            // Usamos intervention/image si estuviera disponible, pero usaremos storage raw
            // Forzamos la extensión a .jpg renombrando si es necesario al guardar
            // Para simplicidad en este entorno sin BD, guardamos directamente
            $path = $file->storeAs('avatars', $filename, 'public');

            return response()->json([
                'success' => true, 
                'message' => 'Avatar actualizado correctamente',
                'path' => Storage::url($path) . '?v=' . time()
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se subió ningún archivo'], 400);
    }

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