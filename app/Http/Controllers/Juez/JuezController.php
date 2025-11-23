<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Equipo, Evento, Proyecto};
use Illuminate\Support\Facades\Auth;


class JuezController extends Controller
{
    public function index()
    {
        // 1. Obtener eventos activos
        // Idealmente filtraríamos por $user->eventosAsignados, pero usamos todos los activos por ahora
        $eventos = Evento::where('fecha_fin', '>=', now())
            ->withCount('proyectos') // Para mostrar "10 Equipos" en la tarjeta
            ->get();

        // 2. Calcular métricas globales para el Juez
        $juezId = Auth::id();
        $totalProyectos = 0;
        $proyectosEvaluados = 0;

        foreach ($eventos as $evento) {
            $proyectos = $evento->proyectos; // Usando relación through equipos si es necesario
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
    public function showEvento(Evento $evento)
    {
        $juezId = Auth::id();

        // CORRECCIÓN: Cargamos 'proyectos' y sus relaciones (equipo y calificaciones)
        $evento->load([
            'proyectos.equipo.participantes', // Para mostrar integrantes
            'proyectos.calificaciones' => function ($q) use ($juezId) {
                $q->where('juez_user_id', $juezId);
            }
        ]);

        return view('juez.eventos.show', compact('evento'));
    }
}
