<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use App\Http\Requests\Juez\EvaluarProyectoRequest;
use App\Models\Calificacion;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    /**
     * Muestra el formulario de evaluación.
     * 
     * Cargar criterios del evento y si ya existen calificaciones previas de ESTE juez.
     * Mapear calificaciones previas para fácil acceso en la vista (Key: criterio_id => Value: puntos).
     */
    public function edit(Proyecto $proyecto)
    {
        // <<< NEW: Manual authorization for edit method >>>
        $user = Auth::user();
        if (!$user->hasRole('Juez') || !$user->eventosAsignados->contains($proyecto->evento)) {
            abort(403, 'No tienes permiso para evaluar proyectos de este evento.');
        }

        $proyecto->load([
            'evento.criterios',
            'equipo',
            'calificaciones' => function ($q) use ($user) {
                $q->where('juez_user_id', $user->id);
            }
        ]);

        $calificacionesPrevias = $proyecto->calificaciones->pluck('puntuacion', 'criterio_id')->toArray();

        $comentarioPrevio = \App\Models\EvaluacionComentario::where('proyecto_id', $proyecto->id)
            ->where('juez_user_id', $user->id)
            ->first();

        $comentarioTexto = $comentarioPrevio ? $comentarioPrevio->comentario : '';

        return view('juez.evaluaciones.edit', compact('proyecto', 'calificacionesPrevias', 'comentarioTexto'));
    }

    /**
     * Almacena la evaluación.
     * 
     * Usamos EvaluarProyectoRequest que maneja la autorización y validación.
     */
    public function store(EvaluarProyectoRequest $request, Proyecto $proyecto)
    {
        try {
            DB::transaction(function () use ($request, $proyecto) {
                $juezId = Auth::id();
                $validated = $request->validated();

                foreach ($validated['puntuaciones'] as $criterioId => $puntuacion) {
                    Calificacion::updateOrCreate(
                        ['proyecto_id' => $proyecto->id, 'juez_user_id' => $juezId, 'criterio_id' => $criterioId],
                        ['puntuacion' => $puntuacion]
                    );
                }

                if (isset($validated['comentario'])) {
                    \App\Models\EvaluacionComentario::updateOrCreate(
                        ['proyecto_id' => $proyecto->id, 'juez_user_id' => $juezId],
                        ['comentario' => $validated['comentario']]
                    );
                }
            });

            return redirect()->route('juez.dashboard')->with('success', 'Evaluación y feedback guardados.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
