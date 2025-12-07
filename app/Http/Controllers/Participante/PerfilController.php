<?php

namespace App\Http\Controllers\Participante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function create()
    {
        $carreras = \App\Models\Carrera::all();

        // Pasamos el perfil actual (si existe) a la vista para pre-llenar datos
        $perfil = Auth::user()->participante;

        return view('participante.registro', compact('carreras', 'perfil'));
    }

    public function store(Request $request)
    {
        // Obtenemos el usuario actual
        $user = $request->user();

        // Validación dinámica:
        // Si el usuario YA tiene un No. Control registrado a su nombre, no validamos 'unique' estrictamente contra todos,
        // pero para simplificar, usamos Rule::unique ignorando al ID del participante actual si existe.

        $participanteId = $user->participante ? $user->participante->id : null;

        $request->validate([
            'no_control' => ['required', 'size:8', 'regex:/^[a-zA-Z0-9]{8}$/', \Illuminate\Validation\Rule::unique('participantes')->ignore($participanteId)],
            'carrera_id' => 'required|exists:carreras,id',
            'telefono'   => 'required|digits:10',
        ], [
            'no_control.size' => 'La matrícula debe tener exactamente 8 caracteres.',
            'no_control.regex' => 'La matrícula solo puede contener letras y números.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
        ]);

        // updateOrCreate busca por 'user_id' y actualiza o crea según sea necesario
        $user->participante()->updateOrCreate(
            ['user_id' => $user->id], // Condición de búsqueda
            [
                'no_control' => $request->no_control,
                'carrera_id' => $request->carrera_id,
                'telefono'   => $request->telefono,
            ]
        );

        return redirect()->route('participante.dashboard');
    }
}
