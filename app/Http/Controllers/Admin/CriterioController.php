<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CriterioEvaluacion;
use App\Models\Evento;
use Illuminate\Http\Request;

class CriterioController extends Controller
{
    /**
     * Almacena un nuevo criterio de evaluación.
     * 
     * Validacion de datos y validacion del negocio (suma de ponderaciones).
     * Crea el criterio si pasa las validaciones.
     */
    public function store(Request $request, Evento $evento)
    {

        $request->validate([
            'nombre' => 'required|string|max:255',
            'ponderacion' => 'required|integer|min:1|max:100',
        ]);

        $suma_actual = $evento->criterios()->sum('ponderacion');

        if (($suma_actual + $request->ponderacion) > 100) {
            return back()->withErrors(['ponderacion' => 'La suma de ponderaciones superaría el 100%. Actualmente llevas ' . $suma_actual . '%.']);
        }

        $evento->criterios()->create([
            'nombre' => $request->nombre,
            'ponderacion' => $request->ponderacion
        ]);

        return back()->with('success', 'Criterio agregado correctamente.');
    }

    public function edit(CriterioEvaluacion $criterio)
    {
        return view('admin.criterios.edit', compact('criterio'));
    }

    /**
     * Actualiza un criterio existente.
     * 
     * Cálculo matemático: Suma total de los OTROS criterios (excluyendo el actual).
     * Verificamos si el nuevo valor rompe el límite.
     * Redirigimos al detalle del EVENTO, no al criterio.
     */
    public function update(Request $request, CriterioEvaluacion $criterio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ponderacion' => 'required|integer|min:1|max:100',
        ]);

        $evento = $criterio->evento;

        $suma_otros = $evento->criterios()->where('id', '!=', $criterio->id)->sum('ponderacion');

        if (($suma_otros + $request->ponderacion) > 100) {
            return back()->withErrors(['ponderacion' => 'Error: La suma total sería ' . ($suma_otros + $request->ponderacion) . '%. Ajusta los otros criterios primero.']);
        }

        $criterio->update($request->only('nombre', 'ponderacion'));

        return redirect()->route('admin.eventos.show', $evento)->with('success', 'Criterio actualizado.');
    }

    public function destroy(CriterioEvaluacion $criterio)
    {
        $criterio->delete();
        return back()->with('success', 'Criterio eliminado.');
    }
}
