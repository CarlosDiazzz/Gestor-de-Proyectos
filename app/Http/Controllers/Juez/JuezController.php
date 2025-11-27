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
     * Obtener eventos activos.
     * Idealmente filtraríamos por $user->eventosAsignados, pero usamos todos los activos por ahora.
     * Calcular métricas globales para el Juez.
     */
    public function index()
    {
        $eventos = Evento::where('fecha_fin', '>=', now())
            ->withCount('proyectos')
            ->get();

        $juezId = Auth::id();
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

    // Método nuevo para ver el detalle de UN evento (Tu tabla de proyectos va aquí)
    /**
     * Método nuevo para ver el detalle de UN evento (Tu tabla de proyectos va aquí).
     * 
     * CORRECCIÓN: Cargamos 'proyectos' y sus relaciones (equipo y calificaciones).
     */
    public function showEvento(Evento $evento)
    {
        $juezId = Auth::id();

        $evento->load([
            'proyectos.equipo.participantes',
            'proyectos.calificaciones' => function ($q) use ($juezId) {
                $q->where('juez_user_id', $juezId);
            }
        ]);

        return view('juez.eventos.show', compact('evento'));
    }
}
