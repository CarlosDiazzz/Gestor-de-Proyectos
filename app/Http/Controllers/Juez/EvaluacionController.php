<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
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
        $proyecto->load([
            'evento.criterios',
            'equipo',
            'calificaciones' => function ($q) {
                $q->where('juez_user_id', Auth::id());
            }
        ]);

        $calificacionesPrevias = $proyecto->calificaciones->pluck('puntuacion', 'criterio_id')->toArray();

        $comentarioPrevio = \App\Models\EvaluacionComentario::where('proyecto_id', $proyecto->id)
            ->where('juez_user_id', Auth::id())
            ->first();

        $comentarioTexto = $comentarioPrevio ? $comentarioPrevio->comentario : '';

        return view('juez.evaluaciones.edit', compact('proyecto', 'calificacionesPrevias', 'comentarioTexto'));
    }

    /**
     * Almacena la evaluación.
     * 
     * Guardar números (Igual que antes).
     * GUARDAR COMENTARIO (NUEVO).
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'puntuaciones' => 'required|array',
            'puntuaciones.*' => 'required|numeric|min:0|max:100',
            'comentario' => 'nullable|string|max:1000', // Validación del texto
        ]);

        try {
            DB::transaction(function () use ($request, $proyecto) {
                $juezId = Auth::id();

                foreach ($request->puntuaciones as $criterioId => $puntuacion) {
                    \App\Models\Calificacion::updateOrCreate(
                        ['proyecto_id' => $proyecto->id, 'juez_user_id' => $juezId, 'criterio_id' => $criterioId],
                        ['puntuacion' => $puntuacion]
                    );
                }

                if ($request->filled('comentario')) {
                    \App\Models\EvaluacionComentario::updateOrCreate(
                        ['proyecto_id' => $proyecto->id, 'juez_user_id' => $juezId],
                        ['comentario' => $request->comentario]
                    );
                }
            });

            return redirect()->route('juez.dashboard')->with('success', 'Evaluación y feedback guardados.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
