<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Evento;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Muestra el panel de administración con métricas y gráficos.
     * 
     * DATOS PARA GRÁFICO 2: Estado de Evaluación de Proyectos (Barras)
     * Contamos cuántos proyectos tienen calificaciones vs cuántos no.
     */
    public function index()
    {
        $total_jueces = User::whereHas('roles', fn($q) => $q->where('nombre', 'Juez'))->count();
        $total_participantes = User::whereHas('roles', fn($q) => $q->where('nombre', 'Participante'))->count();
        $total_equipos = Equipo::count();
        $total_proyectos = Proyecto::count();

        $eventos_activos = Evento::where('fecha_fin', '>=', now())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        $participantesPorCarrera = DB::table('participantes')
            ->join('carreras', 'participantes.carrera_id', '=', 'carreras.id')
            ->select('carreras.nombre', DB::raw('count(*) as total'))
            ->groupBy('carreras.nombre')
            ->pluck('total', 'nombre');

        $proyectosEvaluados = Proyecto::has('calificaciones')->count();
        $proyectosPendientes = $total_proyectos - $proyectosEvaluados;

        return view('admin.dashboard', compact(
            'total_jueces',
            'total_participantes',
            'total_equipos',
            'eventos_activos',
            'total_proyectos',
            'participantesPorCarrera',
            'proyectosEvaluados',
            'proyectosPendientes'
        ));
    }
}
