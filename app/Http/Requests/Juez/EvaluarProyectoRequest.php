<?php

namespace App\Http\Requests\Juez;

use Illuminate\Foundation\Http\FormRequest;

class EvaluarProyectoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $proyecto = $this->route('proyecto');
        $user = $this->user();

        // El usuario puede evaluar si es Juez Y está asignado al evento del proyecto.
        return $user->hasRole('Juez') && $user->eventosAsignados->contains($proyecto->evento);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'puntuaciones' => 'required|array',
            'puntuaciones.*' => 'required|numeric|min:0|max:100',
            'comentario' => 'nullable|string|max:1000',
        ];
    }
}
