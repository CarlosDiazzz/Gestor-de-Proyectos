<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Equipo, Evento, Proyecto};
use Illuminate\Support\Facades\Auth;


class JuezController extends Controller
{
    /**
     * Muestra el dashboard del juez.
     * 
     * Obtener eventos activos asignados al juez.
     * Calcular métricas globales para el Juez.
     */
    public function index()
    {
        $juez = Auth::user();
        $eventos = $juez->eventosAsignados()
            ->where('fecha_fin', '>=', now())
            ->withCount('proyectos')
            ->get();

        $juezId = $juez->id;
        $totalProyectos = 0;
        $proyectosEvaluados = 0;

        foreach ($eventos as $evento) {
            $proyectos = $evento->proyectos;
            $totalProyectos += $proyectos->count();

            foreach ($proyectos as $p) {
                if ($p->calificaciones()->where('juez_user_id', $juezId)->exists()) {
                    $proyectosEvaluados++;
                }
            }
        }

        $pendientes = $totalProyectos - $proyectosEvaluados;

        return view('juez.dashboard', compact('eventos', 'totalProyectos', 'proyectosEvaluados', 'pendientes'));
    }

    /**
     * Muestra el detalle de UN evento asignado.
     * 
     * CORRECCIÓN: Cargamos 'proyectos' y sus relaciones (equipo y calificaciones).
     * NUEVO: Se verifica que el juez esté asignado al evento.
     */
    public function showEvento(Evento $evento)
    {
        $juez = Auth::user();
        
        // Verificamos si el juez está asignado a este evento
        if (!$juez->eventosAsignados->contains($evento)) {
            abort(403, 'No tienes permiso para ver este evento.');
        }

        $juezId = $juez->id;

        $evento->load([
            'proyectos.equipo.participantes',
            'proyectos.calificaciones' => function ($q) use ($juezId) {
                $q->where('juez_user_id', $juezId);
            }
        ]);

        return view('juez.eventos.show', compact('evento'));
    }
}
