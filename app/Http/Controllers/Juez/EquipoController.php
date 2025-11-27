<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Participante;
use App\Models\Perfil;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    /**
     * Muestra el formulario de edición de equipo.
     * 
     * VALIDACIÓN DE SEGURIDAD (Corregida).
     */
    public function edit(Equipo $equipo)
    {
        $equipo->load('proyecto.evento');

        if (!$equipo->proyecto || !$equipo->proyecto->evento) {
            return back()->with('error', 'Error crítico: Este equipo no tiene un proyecto o evento asociado.');
        }

        if ($equipo->proyecto->evento->fecha_fin < now()) {
            return back()->with('error', 'Este evento ya finalizó, no se pueden editar equipos.');
        }

        $equipo->load(['participantes.user', 'participantes.carrera']);
        $perfiles = Perfil::all();

        $candidatos = Participante::whereDoesntHave('equipos')
            ->with(['user', 'carrera'])
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->user->name,
                'no_control' => $p->no_control,
                'carrera' => $p->carrera->nombre
            ]);

        return view('juez.equipos.edit', compact('equipo', 'perfiles', 'candidatos'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:equipos,nombre,' . $equipo->id,
        ]);

        $equipo->update(['nombre' => $request->nombre]);

        return back()->with('success', 'Nombre del equipo actualizado.');
    }

    /**
     * Agrega un miembro al equipo.
     * 
     * Validar cupo.
     */
    public function addMember(Request $request, Equipo $equipo)
    {
        if ($equipo->participantes()->count() >= 5) {
            return back()->with('error', 'El equipo está lleno.');
        }

        $request->validate([
            'participante_id' => 'required|exists:participantes,id',
            'perfil_id' => 'required|exists:perfiles,id',
        ]);

        $participante = Participante::find($request->participante_id);
        if ($participante->equipos()->exists()) {
            return back()->with('error', 'El alumno ya pertenece a otro equipo.');
        }

        $equipo->participantes()->attach($request->participante_id, [
            'perfil_id' => $request->perfil_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Participante agregado exitosamente.');
    }

    /**
     * Eliminar Miembro (Usando lógica de sucesión de líder).
     * 
     * Usamos el método inteligente del modelo que creamos antes.
     */
    public function removeMember(Equipo $equipo, $participanteId)
    {
        $equipo->removerIntegrante($participanteId);

        return back()->with('success', 'Miembro eliminado. Liderazgo reasignado si fue necesario.');
    }
}
