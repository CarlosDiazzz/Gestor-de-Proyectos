<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudEquipo extends Model
{
    protected $table = 'solicitudes_equipo';

    protected $fillable = [
        'equipo_id',
        'participante_id',
        'perfil_solicitado_id',
        'mensaje',
        'estado',
        'respondida_por_participante_id',
        'respondida_en'
    ];

    protected $casts = [
        'respondida_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class);
    }

    public function respondidaPor()
    {
        return $this->belongsTo(Participante::class, 'respondida_por_participante_id');
    }

    public function perfilSugerido()
    {
        return $this->belongsTo(Perfil::class, 'perfil_solicitado_id');
    }

    // Scopes
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAceptada($query)
    {
        return $query->where('estado', 'aceptada');
    }

    public function scopeRechazada($query)
    {
        return $query->where('estado', 'rechazada');
    }
}

