<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Proyecto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ResultadosController extends Controller
{
    /**
     * Muestra el ranking de proyectos.
     * 
     * Redondeamos a 2 decimales.
     * Ordenar del mejor al peor.
     */
    public function index(Request $request)
    {
        $eventoId = $request->get('evento_id');
        $evento = $eventoId ? Evento::find($eventoId) : Evento::latest()->first();

        $eventos = Evento::all();
        $ranking = collect();

        if ($evento) {
            $proyectos = Proyecto::where('evento_id', $evento->id)
                ->with(['calificaciones', 'equipo'])
                ->get();

            $ranking = $proyectos->map(function ($proyecto) use ($evento) {
                $totalPuntos = 0;
                $calificacionesAgrupadas = $proyecto->calificaciones->groupBy('criterio_id');

                foreach ($evento->criterios as $criterio) {
                    $notas = $calificacionesAgrupadas->get($criterio->id);
                    if ($notas && $notas->count() > 0) {
                        $promedio = $notas->avg('puntuacion');
                        $puntosReales = ($promedio * $criterio->ponderacion) / 100;
                        $totalPuntos += $puntosReales;
                    }
                }

                return (object) [
                    'id' => $proyecto->id,
                    'nombre' => $proyecto->nombre,
                    'equipo' => $proyecto->equipo->nombre,
                    'puntaje' => round($totalPuntos, 2),
                    'integrantes' => $proyecto->equipo->participantes
                ];
            })->sortByDesc('puntaje')->values();
        }

        return view('admin.resultados.index', compact('ranking', 'eventos', 'evento'));
    }

    // Método para descargar Constancia (Lo implementaremos en el siguiente paso)
    /**
     * Método para descargar Constancia.
     * 
     * Generamos el PDF usando una vista Blade.
     */
    public function descargarConstancia($proyectoId, $posicion)
    {
        $proyecto = Proyecto::with(['equipo.participantes.user', 'evento'])->findOrFail($proyectoId);

        $textoLogro = match ($posicion) {
            '1' => 'PRIMER LUGAR',
            '2' => 'SEGUNDO LUGAR',
            '3' => 'TERCER LUGAR',
            default => 'PARTICIPACIÓN DESTACADA'
        };

        $pdf = Pdf::loadView('admin.resultados.pdf', compact('proyecto', 'textoLogro'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Constancia_' . $proyecto->equipo->nombre . '.pdf');
    }
}
