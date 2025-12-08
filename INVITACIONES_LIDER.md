# Sistema de Invitaciones del Líder a Participantes

## 📋 Descripción General
Actualmente el sistema funciona con **SOLICITUDES BIDIRECCIONALES**:
- Un participante SIN equipo envía solicitud al líder para unirse ✅
- El líder ACEPTA o RECHAZA la solicitud ✅

**Ahora implementaremos INVITACIONES**:
- El líder envía invitación a un participante específico 🎯
- El participante VE la invitación en su dashboard 👁️
- El participante ACEPTA o RECHAZA la invitación ✅

---

## 🏗️ Arquitectura Actual

### Tablas en BD
```
solicitudes_equipo
├── equipo_id (FK)
├── participante_id (FK - quien solicita)
├── perfil_solicitado_id (FK)
├── mensaje
├── estado (pendiente|aceptada|rechazada)
├── respondida_por_participante_id (FK)
└── respondida_en (datetime)
```

### Flujo Actual (Solicitudes)
1. **Participante sin equipo** → Va a "Unirme a Equipo"
2. **Selecciona equipo y rol** → Envía solicitud
3. **Líder recibe** → Ve en dashboard (widget "Solicitudes Pendientes")
4. **Líder responde** → Acepta/Rechaza
5. **Participante consulta** → Va a "Mis Solicitudes"

---

## ✨ Nuevo Flujo (Invitaciones)

### Cambios Necesarios

#### 1️⃣ Crear tabla `invitaciones_equipo`
```sql
CREATE TABLE invitaciones_equipo (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    equipo_id BIGINT NOT NULL,
    participante_id BIGINT NOT NULL,
    perfil_sugerido_id BIGINT, -- Rol que el líder sugiere
    mensaje TEXT, -- Mensaje personal del líder
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    enviada_por_participante_id BIGINT NOT NULL, -- ID del líder
    respondida_en DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    FOREIGN KEY (participante_id) REFERENCES participantes(id),
    FOREIGN KEY (perfil_sugerido_id) REFERENCES perfiles(id),
    FOREIGN KEY (enviada_por_participante_id) REFERENCES participantes(id)
);
```

#### 2️⃣ Crear Modelo `InvitacionEquipo`
```php
// app/Models/InvitacionEquipo.php
namespace App\Models;

class InvitacionEquipo extends Model {
    protected $table = 'invitaciones_equipo';
    
    protected $fillable = [
        'equipo_id', 'participante_id', 'perfil_sugerido_id',
        'mensaje', 'estado', 'enviada_por_participante_id', 'respondida_en'
    ];
    
    // Relaciones
    public function equipo() {
        return $this->belongsTo(Equipo::class);
    }
    
    public function participante() {
        return $this->belongsTo(Participante::class);
    }
    
    public function enviadaPor() {
        return $this->belongsTo(Participante::class, 'enviada_por_participante_id');
    }
    
    public function perfilSugerido() {
        return $this->belongsTo(Perfil::class, 'perfil_sugerido_id');
    }
    
    // Scopes
    public function scopePendiente($query) {
        return $query->where('estado', 'pendiente');
    }
}
```

#### 3️⃣ Actualizar Modelo `Participante`
```php
// En app/Models/Participante.php

public function invitaciones() {
    return $this->hasMany(InvitacionEquipo::class);
}

public function invitacionesPendientes() {
    return $this->invitaciones()->where('estado', 'pendiente');
}
```

#### 4️⃣ Actualizar Modelo `Equipo`
```php
// En app/Models/Equipo.php

public function invitaciones() {
    return $this->hasMany(InvitacionEquipo::class);
}

public function invitacionesPendientes() {
    return $this->invitaciones()->where('estado', 'pendiente');
}
```

---

## 🎮 Controlador: `InvitacionEquipoController`

```php
// app/Http/Controllers/Participante/InvitacionEquipoController.php
<?php

namespace App\Http\Controllers\Participante;

use App\Http\Controllers\Controller;
use App\Models\{Equipo, Participante, InvitacionEquipo};
use Illuminate\Http\Request;

class InvitacionEquipoController extends Controller
{
    /**
     * Líder invita a un participante
     * GET /participante/equipos/{equipo}/invitar
     */
    public function showInvitarForm(Request $request, Equipo $equipo)
    {
        $participante = $request->user()->participante;
        $lider = $equipo->getLider();

        // Verificar permisos
        if (!$lider || $lider->id !== $participante->id) {
            return back()->with('error', 'Solo el líder puede enviar invitaciones.');
        }

        // Obtener participantes sin equipo
        $participantesSinEquipo = Participante::whereDoesntHave('equipos')
            ->with('user', 'carrera')
            ->get();

        $rolesDisponibles = $equipo->getRolesDisponibles();

        return view('participante.invitaciones.enviar', compact(
            'equipo', 'participantesSinEquipo', 'rolesDisponibles'
        ));
    }

    /**
     * Guardar invitación
     * POST /participante/equipos/{equipo}/invitar
     */
    public function enviarInvitacion(Request $request, Equipo $equipo)
    {
        $request->validate([
            'participante_id' => 'required|exists:participantes,id',
            'perfil_sugerido_id' => 'nullable|exists:perfiles,id',
            'mensaje' => 'nullable|string|max:500',
        ]);

        $lider = $request->user()->participante;

        // Verificar que sea líder
        if ($equipo->getLider()->id !== $lider->id) {
            return back()->with('error', 'No tienes permisos.');
        }

        // Verificar que el equipo tenga espacio
        if ($equipo->estaCompleto()) {
            return back()->with('error', 'El equipo está completo.');
        }

        // Verificar que el participante exista y no esté en otro equipo
        $participante = Participante::findOrFail($request->participante_id);
        if ($participante->equipos->isNotEmpty()) {
            return back()->with('error', 'Este participante ya está en un equipo.');
        }

        // Verificar que no exista invitación previa PENDIENTE
        $invitacionExistente = InvitacionEquipo::where('equipo_id', $equipo->id)
            ->where('participante_id', $participante->id)
            ->where('estado', 'pendiente')
            ->first();

        if ($invitacionExistente) {
            return back()->with('error', 'Ya existe una invitación pendiente para este participante.');
        }

        // Crear invitación
        $invitacion = InvitacionEquipo::create([
            'equipo_id' => $equipo->id,
            'participante_id' => $participante->id,
            'perfil_sugerido_id' => $request->perfil_sugerido_id,
            'mensaje' => $request->mensaje,
            'estado' => 'pendiente',
            'enviada_por_participante_id' => $lider->id,
        ]);

        // Disparar evento
        event(new \App\Events\InvitacionEquipoEnviada($invitacion));

        return redirect()->route('participante.equipos.edit', $equipo)
            ->with('success', 'Invitación enviada correctamente.');
    }

    /**
     * Ver invitaciones pendientes del participante
     * GET /participante/invitaciones
     */
    public function misInvitaciones(Request $request)
    {
        $participante = $request->user()->participante;
        
        $invitaciones = $participante->invitaciones()
            ->with('equipo.proyecto', 'enviadaPor.user', 'perfilSugerido')
            ->latest()
            ->paginate(10);

        return view('participante.invitaciones.mis-invitaciones', compact('invitaciones'));
    }

    /**
     * Participante ACEPTA invitación
     * POST /participante/invitaciones/{invitacion}/aceptar
     */
    public function aceptar(Request $request, InvitacionEquipo $invitacion)
    {
        $participante = $request->user()->participante;

        // Verificar permisos
        if ($invitacion->participante_id !== $participante->id) {
            return back()->with('error', 'No tienes permisos.');
        }

        // Verificar que sea pendiente
        if ($invitacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta invitación ya ha sido respondida.');
        }

        // Verificar que el equipo aún tenga espacio
        if ($invitacion->equipo->estaCompleto()) {
            return back()->with('error', 'El equipo ya está completo.');
        }

        // Verificar que el rol tenga vacantes
        if ($invitacion->perfil_sugerido_id && !$invitacion->equipo->tieneVacantesParaRol($invitacion->perfil_sugerido_id)) {
            return back()->with('error', 'El rol sugerido ya no tiene vacantes.');
        }

        // Aceptar invitación
        $invitacion->update([
            'estado' => 'aceptada',
            'respondida_en' => now()
        ]);

        // Agregar al equipo
        $perfilId = $invitacion->perfil_sugerido_id ?? 1; // Default: Programador
        $invitacion->equipo->participantes()->attach(
            $participante->id,
            ['perfil_id' => $perfilId]
        );

        // Rechazar otras invitaciones pendientes de este participante
        InvitacionEquipo::where('participante_id', $participante->id)
            ->where('estado', 'pendiente')
            ->where('id', '!=', $invitacion->id)
            ->update(['estado' => 'rechazada', 'respondida_en' => now()]);

        event(new \App\Events\InvitacionEquipoAceptada($invitacion));

        return back()->with('success', 'Has aceptado la invitación al equipo.');
    }

    /**
     * Participante RECHAZA invitación
     * POST /participante/invitaciones/{invitacion}/rechazar
     */
    public function rechazar(Request $request, InvitacionEquipo $invitacion)
    {
        $participante = $request->user()->participante;

        // Verificar permisos
        if ($invitacion->participante_id !== $participante->id) {
            return back()->with('error', 'No tienes permisos.');
        }

        // Verificar que sea pendiente
        if ($invitacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta invitación ya ha sido respondida.');
        }

        // Rechazar
        $invitacion->update([
            'estado' => 'rechazada',
            'respondida_en' => now()
        ]);

        event(new \App\Events\InvitacionEquipoRechazada($invitacion));

        return back()->with('success', 'Invitación rechazada.');
    }

    /**
     * Líder ve invitaciones enviadas
     * GET /participante/equipos/{equipo}/invitaciones/enviadas
     */
    public function invitacionesEnviadas(Request $request, Equipo $equipo)
    {
        $participante = $request->user()->participante;
        $lider = $equipo->getLider();

        if (!$lider || $lider->id !== $participante->id) {
            return back()->with('error', 'Solo el líder puede ver esto.');
        }

        $invitaciones = $equipo->invitaciones()
            ->with('participante.user', 'perfilSugerido')
            ->latest()
            ->paginate(10);

        return view('participante.invitaciones.enviadas', compact('equipo', 'invitaciones'));
    }
}
```

---

## 📍 Rutas (web.php)

```php
// En routes/web.php, dentro del grupo de participante:

Route::controller(InvitacionEquipoController::class)->prefix('invitaciones')->name('invitaciones.')->group(function () {
    // Para participantes: ver y responder invitaciones
    Route::get('/mis-invitaciones', 'misInvitaciones')->name('mis');
    Route::post('/{invitacion}/aceptar', 'aceptar')->name('aceptar');
    Route::post('/{invitacion}/rechazar', 'rechazar')->name('rechazar');
});

// Para líder: enviar invitaciones (modificar ruta de equipos)
Route::post('/equipo/{equipo}/invitar', [InvitacionEquipoController::class, 'enviarInvitacion'])
    ->name('invitaciones.enviar');
Route::get('/equipo/{equipo}/invitar', [InvitacionEquipoController::class, 'showInvitarForm'])
    ->name('invitaciones.form');
Route::get('/equipo/{equipo}/invitaciones-enviadas', [InvitacionEquipoController::class, 'invitacionesEnviadas'])
    ->name('invitaciones.enviadas');
```

---

## 🎨 Vistas Necesarias

### 1. Vista: `enviar.blade.php` (Líder invita)
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Invitar a Participante</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">{{ $equipo->nombre }}</h3>
                
                <form action="{{ route('participante.invitaciones.enviar', $equipo) }}" method="POST">
                    @csrf
                    
                    {{-- Seleccionar participante --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold">Participante</label>
                        <select name="participante_id" required class="mt-1 block w-full">
                            <option value="">Selecciona un participante</option>
                            @foreach($participantesSinEquipo as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->user->name }} ({{ $p->no_control }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rol sugerido --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold">Rol Sugerido (opcional)</label>
                        <select name="perfil_sugerido_id" class="mt-1 block w-full">
                            <option value="">Sin especificar</option>
                            @foreach($rolesDisponibles as $rol)
                                <option value="{{ $rol['id'] }}">
                                    {{ $rol['nombre'] }} ({{ $rol['disponibles'] }} vacantes)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Mensaje --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold">Mensaje (opcional)</label>
                        <textarea name="mensaje" rows="4" class="mt-1 block w-full"></textarea>
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
                        Enviar Invitación
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 2. Vista: `mis-invitaciones.blade.php` (Participante ve invitaciones)
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Mis Invitaciones</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto">
            @if($invitaciones->isEmpty())
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-500">No tienes invitaciones pendientes</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($invitaciones as $inv)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold">{{ $inv->equipo->nombre }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Invitado por {{ $inv->enviadaPor->user->name }}
                                    </p>
                                    @if($inv->perfilSugerido)
                                        <p class="text-sm mt-2">
                                            <strong>Rol:</strong> {{ $inv->perfilSugerido->nombre }}
                                        </p>
                                    @endif
                                </div>
                                
                                @if($inv->estado === 'pendiente')
                                    <div class="flex gap-2">
                                        <form action="{{ route('participante.invitaciones.aceptar', $inv) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-600 text-white px-4 py-2 rounded">
                                                Aceptar
                                            </button>
                                        </form>
                                        <form action="{{ route('participante.invitaciones.rechazar', $inv) }}" method="POST">
                                            @csrf
                                            <button class="bg-red-600 text-white px-4 py-2 rounded">
                                                Rechazar
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm font-bold 
                                        @if($inv->estado === 'aceptada') bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($inv->estado) }}
                                    </span>
                                @endif
                            </div>
                            
                            @if($inv->mensaje)
                                <div class="mt-4 p-3 bg-gray-50 rounded border border-gray-200">
                                    <p class="text-sm text-gray-600">{{ $inv->mensaje }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
```

---

## 🔔 Eventos Sugeridos

```php
// app/Events/InvitacionEquipoEnviada.php
namespace App\Events;

use App\Models\InvitacionEquipo;
use Illuminate\Queue\SerializesModels;

class InvitacionEquipoEnviada {
    use SerializesModels;
    public $invitacion;
    
    public function __construct(InvitacionEquipo $invitacion) {
        $this->invitacion = $invitacion;
    }
}

// app/Listeners/NotificarInvitacionEquipo.php
// (Enviar notificación por email/SMS)
```

---

## 📊 Cambios en Dashboard

En `ParticipanteController@index`:
- Mostrar **invitaciones pendientes** en un widget similar a las solicitudes
- Link a "Mis Invitaciones"

En vista dashboard:
```php
// Agregar al dashboard
$invitacionesPendientes = $participante->invitacionesPendientes()
    ->with('equipo', 'enviadaPor.user')
    ->get();

// Pasar a vista
return view('participante.dashboard', compact(
    ...
    'invitacionesPendientes'
));
```

---

## ✅ Resumen de Cambios

| Componente | Acción |
|-----------|--------|
| **BD** | Crear tabla `invitaciones_equipo` |
| **Modelos** | Crear `InvitacionEquipo`, actualizar `Participante` y `Equipo` |
| **Controlador** | Crear `InvitacionEquipoController` |
| **Rutas** | Agregar rutas para invitaciones |
| **Vistas** | Crear 3 vistas (enviar, mis-invitaciones, enviadas) |
| **Dashboard** | Mostrar widget con invitaciones pendientes |
| **Eventos** | Crear eventos para notificaciones |

---

## 🎯 Flujo Completo de Invitación

```
1. LÍDER EN EDIT EQUIPO
   ↓
   [Botón "Invitar Participante"]
   ↓
2. FORMULARIO INVITAR
   - Seleccionar participante sin equipo
   - Sugerir rol
   - Mensaje personal
   ↓
3. GUARDAR INVITACIÓN
   - Crear registro en invitaciones_equipo
   - Estado: "pendiente"
   ↓
4. PARTICIPANTE VE
   - Widget en dashboard: "Invitaciones Pendientes"
   - Link a "Mis Invitaciones"
   ↓
5. PARTICIPANTE RESPONDE
   - ACEPTAR → Se agrega al equipo
   - RECHAZAR → Invitación rechazada
   ↓
6. LÍDER VE (opcional)
   - Historial de invitaciones enviadas
   - Estado de cada una
```
