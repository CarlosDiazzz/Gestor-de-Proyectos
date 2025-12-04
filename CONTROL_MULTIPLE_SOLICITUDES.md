# 🔒 Control de Solicitudes Múltiples - Documentación Técnica

## Problema Solucionado

Cuando un participante intenta enviar múltiples solicitudes o se acepta una solicitud, necesitamos:
1. ✅ Prevenir que envíe múltiples solicitudes al mismo equipo
2. ✅ Rechazar automáticamente otras solicitudes cuando se acepta una
3. ✅ Mantener integridad en la base de datos

---

## 1. Prevención de Múltiples Solicitudes al Mismo Equipo

### Ubicación: `EquipoController.php` - Método `join()`

```php
// Validación 3: Que no tenga solicitud pendiente para este equipo
$solicitudPendiente = SolicitudEquipo::where('equipo_id', $equipo->id)
    ->where('participante_id', $participante->id)
    ->where('estado', 'pendiente')
    ->exists();

if ($solicitudPendiente) {
    return back()->with('error', 'Ya tienes una solicitud pendiente para este equipo.');
}
```

**Flujo:**
1. Participante intenta enviar solicitud a Equipo A
2. Sistema verifica si ya tiene solicitud pendiente para Equipo A
3. Si existe → Rechaza con error
4. Si no existe → Permite continuar a formulario

---

## 2. Prevención de Múltiples Solicitudes al Crear

### Ubicación: `SolicitudEquipoController.php` - Método `crearSolicitud()`

```php
// Validar que no haya solicitud pendiente PARA ESTE EQUIPO
if (SolicitudEquipo::where('equipo_id', $equipo->id)
    ->where('participante_id', $participante->id)
    ->where('estado', 'pendiente')
    ->exists()) {
    return redirect()->route('participante.dashboard')
        ->with('error', 'Ya tienes una solicitud pendiente para este equipo.');
}
```

**Flujo:**
1. Participante completa formulario y hace POST
2. Sistema verifica nuevamente si existe solicitud pendiente
3. Si existe → Rechaza (doble validación por seguridad)
4. Si no → Crea solicitud

---

## 3. Rechazo Automático de Otras Solicitudes

### Ubicación: `SolicitudEquipoController.php` - Método `aceptar()`

```php
// AUTOMÁTICAMENTE: Rechazar todas las otras solicitudes pendientes de este participante
SolicitudEquipo::where('participante_id', $solicitud->participante_id)
    ->where('estado', 'pendiente')
    ->where('id', '!=', $solicitud->id)  // Excepto la que se acepta
    ->update([
        'estado' => 'rechazada',
        'respondida_por_participante_id' => $lider->id,
        'respondida_en' => now()
    ]);
```

**Flujo:**
1. Líder acepta solicitud de Participante X para Equipo A
2. Sistema actualiza solicitud a "aceptada"
3. Sistema agrega participante al equipo
4. Sistema busca TODAS las solicitudes pendientes del Participante X
5. Las rechaza automáticamente (excepto la que se acabó de aceptar)

**Ejemplo:**
```
Participante X envía:
├─ Solicitud A → Equipo A (pendiente)
├─ Solicitud B → Equipo B (pendiente)
└─ Solicitud C → Equipo C (pendiente)

Líder de Equipo A ACEPTA:
├─ Solicitud A → ACEPTADA ✅ (Participante se agrega)
├─ Solicitud B → RECHAZADA ❌ (automático)
└─ Solicitud C → RECHAZADA ❌ (automático)
```

---

## 4. Redirección al Dashboard

### Ubicación: `SolicitudEquipoController.php` - Método `crearSolicitud()`

```php
return redirect()->route('participante.dashboard')
    ->with('success', 'Solicitud enviada al líder del equipo.');
```

**Flujo:**
1. Se crea la solicitud exitosamente
2. Se dispara evento `SolicitudEquipoEnviada`
3. Se redirige al dashboard (no se queda en la vista)
4. Mensaje: "Solicitud enviada al líder del equipo"

---

## 5. Validaciones en Cascada

```
EquipoController.join()
    ├─ ¿Está en otro equipo? → Error
    ├─ ¿Equipo está lleno? → Error
    ├─ ¿Solicitud pendiente para este equipo? → Error
    └─ ✅ Todo bien → Redirect a formulario

SolicitudEquipoController.showCrearSolicitud()
    └─ ✅ Muestra formulario

SolicitudEquipoController.crearSolicitud()
    ├─ ¿Está en este equipo? → Error
    ├─ ¿Está en otro equipo? → Error
    ├─ ¿Solicitud pendiente? → Error
    └─ ✅ Todo bien → Crea solicitud → Redirect dashboard
```

---

## 6. Estados y Transiciones

```
PENDIENTE ─────────────┬────────────────┐
                       │                │
                     Acepta          Rechaza
                       │                │
                       ▼                ▼
                   ACEPTADA        RECHAZADA
                   (Final)          (Final)
```

**Importante:** Una vez que una solicitud pasa a ACEPTADA o RECHAZADA, no puede cambiar.

---

## 7. Integridad de Base de Datos

### Constraints en Tabla `solicitudes_equipo`

```sql
UNIQUE (equipo_id, participante_id)
```

Esto previene duplicados a nivel de BD.

### Relaciones Configuradas

```php
// En SolicitudEquipo.php
public function equipo() {
    return $this->belongsTo(Equipo::class);
}

public function participante() {
    return $this->belongsTo(Participante::class);
}

public function respondidaPor() {
    return $this->belongsTo(Participante::class, 'respondida_por_participante_id');
}
```

---

## 8. Flujo Completo de Ejemplo

### Scenario: Participante Juan envía 3 solicitudes

```
1. Juan va a /unirse-equipo

2. Envía solicitud a EQUIPO A
   ✅ No tiene solicitud pendiente para EQUIPO A
   → Se crea: solicitudes_equipo {
       equipo_id: 1,
       participante_id: 5,
       mensaje: "Quiero unirme",
       estado: "pendiente"
     }
   → Redirect: /participante/dashboard

3. Intenta enviar otra solicitud a EQUIPO A
   ❌ Validación 1: Ya existe solicitud pendiente
   → Error: "Ya tienes una solicitud pendiente para este equipo"
   → No se crea duplicado

4. Envía solicitud a EQUIPO B
   ✅ No tiene solicitud pendiente para EQUIPO B
   → Se crea: solicitudes_equipo {
       equipo_id: 2,
       participante_id: 5,
       estado: "pendiente"
     }

5. Envía solicitud a EQUIPO C
   ✅ Mismo proceso
   → Se crea solicitud para EQUIPO C

Estado en BD:
├─ Solicitud → EQUIPO A → PENDIENTE
├─ Solicitud → EQUIPO B → PENDIENTE
└─ Solicitud → EQUIPO C → PENDIENTE

6. Líder de EQUIPO A ACEPTA solicitud
   → Solicitud EQUIPO A → ACEPTADA
   → Juan se agrega a EQUIPO A
   
   ✅ Automáticamente:
   → Solicitud EQUIPO B → RECHAZADA
   → Solicitud EQUIPO C → RECHAZADA

Estado final en BD:
├─ Solicitud → EQUIPO A → ACEPTADA ✅
├─ Solicitud → EQUIPO B → RECHAZADA ❌
└─ Solicitud → EQUIPO C → RECHAZADA ❌
```

---

## 9. Mensajes de Error Implementados

| Error | Causante | Ubicación |
|-------|----------|-----------|
| "Ya tienes equipo" | Está en otro equipo | `join()` |
| "Equipo lleno" | Equipo tiene 5 participantes | `join()` |
| "Ya tienes una solicitud pendiente" | Solicitud anterior sin responder | `join()` |
| "Ya estás en este equipo" | Ya es miembro | `crearSolicitud()` |
| "Ya estás en otro equipo" | Pertenece a otro | `crearSolicitud()` |

---

## 10. Testing

### Caso 1: Prevenir solicitud duplicada
```
1. Participante X envía solicitud a Equipo A
2. Intenta enviar otra a Equipo A
3. ❌ Sistema rechaza
4. ✅ Solo 1 solicitud en BD
```

### Caso 2: Rechazo automático
```
1. Participante X envía a A, B, C (3 solicitudes)
2. Líder de A acepta
3. ✅ Solicitud A → ACEPTADA
4. ✅ Solicitud B → RECHAZADA automático
5. ✅ Solicitud C → RECHAZADA automático
```

### Caso 3: Redirección correcta
```
1. Participante completa formulario
2. Hace submit
3. ✅ Redirect a dashboard
4. ✅ Ver mensaje de éxito
```

---

## 📝 Resumen de Cambios

| Archivo | Cambio |
|---------|--------|
| `SolicitudEquipoController.php` | ✅ `crearSolicitud()` - redirect a dashboard |
| `SolicitudEquipoController.php` | ✅ `aceptar()` - rechaza automáticamente otras |
| `EquipoController.php` | ✅ `join()` - valida solicitud pendiente |
| `EquipoController.php` | ✅ Importa SolicitudEquipo |

---

## 🚀 Resultado Final

✅ **No hay duplicados** - Validaciones en cascada  
✅ **Integridad de BD** - Constraint UNIQUE  
✅ **UX mejorada** - Redirect al dashboard  
✅ **Lógica automática** - Rechaza otras al aceptar  
✅ **Mensajes claros** - Errores descriptivos

