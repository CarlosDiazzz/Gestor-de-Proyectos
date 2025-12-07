<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoRolLimite extends Model
{
    use HasFactory;

    protected $table = 'equipo_rol_limites';

    protected $fillable = [
        'equipo_id',
        'perfil_id',
        'max_vacantes',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
}
