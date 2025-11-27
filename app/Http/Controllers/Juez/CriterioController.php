<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use App\Models\CriterioEvaluacion;
use Illuminate\Http\Request;

class CriterioController extends Controller
{
    /**
     * Almacena un nuevo criterio.
     * 
     * Calcular espacio disponible.
     * Validar si cabe.
     * Creamos solo con los datos necesarios.
     */
    public function store(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'nombre' => 'required|string|max:50',
            'ponderacion' => 'required|integer|min:1|max:100',
        ]);

        $sumaActual = CriterioEvaluacion::where('evento_id', $request->evento_id)->sum('ponderacion');
        $disponible = 100 - $sumaActual;

        if ($request->ponderacion > $disponible) {
            return back()->with('error', "No puedes asignar {$request->ponderacion}%. Solo queda disponible: {$disponible}%.");
        }

        CriterioEvaluacion::create($request->only(['evento_id', 'nombre', 'ponderacion']));

        return back()->with('success', 'Criterio agregado.');
    }

    /**
     * Actualiza un criterio.
     * 
     * Calcular suma de los demás.
     * Validar.
     */
    public function update(Request $request, $id)
    {
        $criterio = CriterioEvaluacion::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50',
            'ponderacion' => 'required|integer|min:1|max:100',
        ]);

        $sumaOtros = CriterioEvaluacion::where('evento_id', $criterio->evento_id)
            ->where('id', '!=', $id)
            ->sum('ponderacion');

        $disponible = 100 - $sumaOtros;

        if ($request->ponderacion > $disponible) {
            return back()->with('error', "Error: La suma total excedería el 100%. Máximo permitido para este criterio: {$disponible}%.");
        }

        $criterio->update($request->only(['nombre', 'ponderacion']));

        return back()->with('success', 'Criterio actualizado.');
    }

    public function destroy($id)
    {
        $criterio = CriterioEvaluacion::findOrFail($id);
        $criterio->delete();
        return back()->with('success', 'Criterio eliminado.');
    }
}
