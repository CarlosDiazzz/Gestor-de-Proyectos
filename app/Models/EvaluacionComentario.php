<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionComentario extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_comentarios';

    protected $fillable = [
        'proyecto_id',
        'juez_user_id',
        'comentario',
    ];

    public function juez()
    {
        return $this->belongsTo(User::class, 'juez_user_id');
    }
}
