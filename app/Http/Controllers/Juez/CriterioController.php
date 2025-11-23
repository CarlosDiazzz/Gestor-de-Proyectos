<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use App\Models\CriterioEvaluacion;
use Illuminate\Http\Request;

class CriterioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'evento_id'   => 'required|exists:eventos,id',
            'nombre'      => 'required|string|max:50',
            'ponderacion' => 'required|integer|min:1|max:100',
        ]);

        // 1. Calcular espacio disponible
        $sumaActual = CriterioEvaluacion::where('evento_id', $request->evento_id)->sum('ponderacion');
        $disponible = 100 - $sumaActual;

        // 2. Validar si cabe
        if ($request->ponderacion > $disponible) {
            return back()->with('error', "No puedes asignar {$request->ponderacion}%. Solo queda disponible: {$disponible}%.");
        }

        // Creamos solo con los datos necesarios
        CriterioEvaluacion::create($request->only(['evento_id', 'nombre', 'ponderacion']));

        return back()->with('success', 'Criterio agregado.');
    }

    public function update(Request $request, $id)
    {
        $criterio = CriterioEvaluacion::findOrFail($id);
        
        $request->validate([
            'nombre'      => 'required|string|max:50',
            'ponderacion' => 'required|integer|min:1|max:100',
        ]);

        // 1. Calcular suma de los demás
        $sumaOtros = CriterioEvaluacion::where('evento_id', $criterio->evento_id)
                        ->where('id', '!=', $id)
                        ->sum('ponderacion');
        
        $disponible = 100 - $sumaOtros;

        // 2. Validar
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
