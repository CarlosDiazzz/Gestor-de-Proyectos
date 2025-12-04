<?php

namespace App\Http\Controllers\Participante;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\SolicitudEquipo;
use App\Events\SolicitudEquipoEnviada;
use App\Events\SolicitudEquipoAceptada;
use App\Events\SolicitudEquipoRechazada;
use Illuminate\Http\Request;

class SolicitudEquipoController extends Controller
{
    public function showCrearSolicitud(Request $request, Equipo $equipo)
    {
        $participante = $request->user()->participante;

        return view('participante.solicitudes.crear-solicitud', compact('equipo', 'participante'));
    }

    public function crearSolicitud(Request $request, Equipo $equipo)
    {
        $request->validate([
            'mensaje' => 'nullable|string|max:500',
        ]);

        $participante = $request->user()->participante;

        // Validar que no esté en el equipo
        if ($participante->equipos->contains($equipo->id)) {
            return redirect()->route('participante.dashboard')->with('error', 'Ya estás en este equipo.');
        }

        // Validar que no esté en otro equipo
        if ($participante->equipos->isNotEmpty()) {
            return redirect()->route('participante.dashboard')->with('error', 'Ya estás en otro equipo. Debes salirte primero.');
        }

        // Validar que no haya solicitud pendiente PARA ESTE EQUIPO
        if (SolicitudEquipo::where('equipo_id', $equipo->id)
            ->where('participante_id', $participante->id)
            ->where('estado', 'pendiente')
            ->exists()) {
            return redirect()->route('participante.dashboard')->with('error', 'Ya tienes una solicitud pendiente para este equipo.');
        }

        // Crear solicitud
        $solicitud = SolicitudEquipo::create([
            'equipo_id' => $equipo->id,
            'participante_id' => $participante->id,
            'mensaje' => $request->mensaje,
            'estado' => 'pendiente'
        ]);

        // Disparar evento
        event(new SolicitudEquipoEnviada($solicitud));

        return redirect()->route('participante.dashboard')->with('success', 'Solicitud enviada al líder del equipo.');
    }

    public function misSolicitudes(Request $request)
    {
        $participante = $request->user()->participante;
        
        $solicitudes = $participante->solicitudes()
            ->with('equipo', 'respondidaPor.user')
            ->latest()
            ->paginate(10);

        return view('participante.solicitudes.mis-solicitudes', compact('solicitudes'));
    }

    public function verSolicitudesEquipo(Request $request, Equipo $equipo)
    {
        $participante = $request->user()->participante;
        $lider = $equipo->getLider();

        // Verificar que sea líder
        if (!$lider || $lider->id !== $participante->id) {
            return back()->with('error', 'No tienes permisos para ver estas solicitudes.');
        }

        $solicitudes = $equipo->solicitudesPendientes()
            ->with('participante.user', 'participante.carrera')
            ->latest()
            ->paginate(10);

        return view('participante.solicitudes.equipo-solicitudes', compact('equipo', 'solicitudes'));
    }

    public function aceptar(Request $request, SolicitudEquipo $solicitud)
    {
        $lider = $request->user()->participante;

        // Verificar que sea el líder del equipo
        if ($solicitud->equipo->getLider()->id !== $lider->id) {
            return back()->with('error', 'No tienes permisos para aceptar esta solicitud.');
        }

        // Verificar que sea pendiente
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya ha sido respondida.');
        }

        // Aceptar solicitud
        $solicitud->update([
            'estado' => 'aceptada',
            'respondida_por_participante_id' => $lider->id,
            'respondida_en' => now()
        ]);

        // Agregar al equipo con perfil de Programador
        $solicitud->equipo->participantes()->attach(
            $solicitud->participante_id,
            ['perfil_id' => 1]
        );

        // AUTOMÁTICAMENTE: Rechazar todas las otras solicitudes pendientes de este participante
        SolicitudEquipo::where('participante_id', $solicitud->participante_id)
            ->where('estado', 'pendiente')
            ->where('id', '!=', $solicitud->id)
            ->update([
                'estado' => 'rechazada',
                'respondida_por_participante_id' => $lider->id,
                'respondida_en' => now()
            ]);

        // Disparar evento
        event(new SolicitudEquipoAceptada($solicitud));

        return back()->with('success', 'Solicitud aceptada. El participante ha sido agregado al equipo.');
    }

    public function rechazar(Request $request, SolicitudEquipo $solicitud)
    {
        $request->validate([
            'razon' => 'nullable|string|max:500',
        ]);

        $lider = $request->user()->participante;

        // Verificar que sea el líder del equipo
        if ($solicitud->equipo->getLider()->id !== $lider->id) {
            return back()->with('error', 'No tienes permisos para rechazar esta solicitud.');
        }

        // Verificar que sea pendiente
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya ha sido respondida.');
        }

        // Rechazar solicitud
        $solicitud->update([
            'estado' => 'rechazada',
            'respondida_por_participante_id' => $lider->id,
            'respondida_en' => now()
        ]);

        // Disparar evento
        event(new SolicitudEquipoRechazada($solicitud));

        return back()->with('success', 'Solicitud rechazada.');
    }
}

