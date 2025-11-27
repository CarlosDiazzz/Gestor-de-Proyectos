<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    /**
     * Muestra la lista de proyectos con filtros.
     */
    public function index(Request $request)
    {
        $query = Proyecto::with(['equipo', 'evento']);

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('evento_id')) {
            $query->where('evento_id', $request->evento_id);
        }

        $proyectos = $query->latest()->paginate(10)->withQueryString();
        $eventos = Evento::all();

        return view('admin.proyectos.index', compact('proyectos', 'eventos'));
    }

    /**
     * Muestra el detalle de un proyecto.
     * 
     * Promedio simple de los jueces (ej: Juez A puso 80, Juez B puso 100 -> Promedio 90).
     * Sumamos al total (Escala final 0 a 100).
     */
    public function show(Proyecto $proyecto)
    {
        $proyecto->load(['equipo.participantes', 'evento.criterios', 'calificaciones']);

        $calificacionesPorCriterio = $proyecto->calificaciones->groupBy('criterio_id');

        $desglosePuntaje = [];
        $puntajeTotal = 0;

        foreach ($proyecto->evento->criterios as $criterio) {
            $notas = $calificacionesPorCriterio->get($criterio->id);

            if ($notas && $notas->count() > 0) {
                $promedioJueces = $notas->avg('puntuacion');

                $puntosObtenidos = ($promedioJueces * $criterio->ponderacion) / 100;

                $desglosePuntaje[] = [
                    'criterio' => $criterio->nombre,
                    'ponderacion' => $criterio->ponderacion,
                    'promedio_jueces' => round($promedioJueces, 1),
                    'puntos_reales' => round(($promedioJueces * ($criterio->ponderacion / 100)), 1)
                ];

                $puntajeTotal += ($promedioJueces * ($criterio->ponderacion / 100));
            } else {
                $desglosePuntaje[] = [
                    'criterio' => $criterio->nombre,
                    'ponderacion' => $criterio->ponderacion,
                    'promedio_jueces' => 0,
                    'puntos_reales' => 0
                ];
            }
        }

        return view('admin.proyectos.show', compact('proyecto', 'desglosePuntaje', 'puntajeTotal'));
    }

    public function edit(Proyecto $proyecto)
    {
        return view('admin.proyectos.edit', compact('proyecto'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'repositorio_url' => 'nullable|url'
        ]);

        $proyecto->update($request->all());

        return redirect()->route('admin.proyectos.show', $proyecto)->with('success', 'Proyecto actualizado.');
    }

    /**
     * Elimina un proyecto (SoftDelete).
     */
    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();
        return redirect()->route('admin.proyectos.index')->with('success', 'Proyecto eliminado.');
    }
}
