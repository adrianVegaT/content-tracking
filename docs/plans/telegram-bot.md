# Plan: telegram bot

## Contexto

[Describir aquí el contexto del problema, archivos involucrados, conexiones,
flujos de datos relevantes, y cualquier detalle del estado actual del sistema
que sea necesario entender antes de implementar.]

[Si hay un flujo de datos importante, representarlo con cadena visual:]

```
input
  └─> componente A → resultado intermedio
       └─> componente B → resultado final
```

## Arquitectura

[Describir cómo se estructura la solución: capas, componentes nuevos,
cómo se integra con lo existente. Numerar puntos clave.]

1. [primer elemento de la arquitectura]
2. [segundo elemento]

## Fases con tareas atomicas

<!--
EJEMPLO de una fase bien hecha (descomentar y adaptar, o eliminar al usar):

### Fase 1: Estructura de carpetas y verificación de conexión
- [ ] Crear app/Repositories/UOnline/
- [ ] Crear app/Services/UOnline/
- [ ] Verificar conexión 'sqlsrv' con un tinker simple
      (DB::connection('sqlsrv')->select('SELECT TOP 1 ...'))

EJEMPLO de fase con convenciones compartidas:

### Fase 2: Modelos read-only
Convenciones para TODOS los modelos de esta fase:
- Ubicación: app/Models/UOnline/
- protected $connection = 'sqlsrv'
- public $timestamps = false
- Sobrescribir save() y delete() para tirar \RuntimeException

Modelos a crear:
- [ ] Persona → tabla 'ra_per_personas', PK 'per_codigo'
- [ ] AlumnoCarrera → tabla 'ra_alc_alumnos_carrera', PK 'alc_codigo'

EJEMPLO de tarea con detalle técnico:

- [ ] StudentAcademicInfoRepository en app/Repositories/UOnline/
      Método: getCareerAndFacultyByCarnet(string $carnet): ?array
      Retorna: ['career' => string, 'faculty' => string] o null si no se encuentra
      Implementación: usar query builder con joins

EJEMPLO de fase opcional/condicional:

### Fase N (opcional): Escalabilidad — descripción
Aplicar solo si [condición]. Umbral sugerido: [criterio].

- [ ] Evaluar [métrica]: si es menor a [umbral], documentar la decisión
      y dejar la fase como referencia futura
-->

### Fase 1: [nombre descriptivo]
- [ ] [tarea atómica 1]
- [ ] [tarea atómica 2]

### Fase 2: [nombre descriptivo]
- [ ] [tarea atómica 1]

## Fuera de alcance:
- [qué NO incluye esta feature, para evitar scope creep]
- [otras features relacionadas que quedan para planes separados]

## Decisiones tomadas

<!--
Formato sugerido (basado en patrón probado):
- **Fase N — título corto**: explicación de la decisión y por qué.

Ejemplos:
- **Fase 1 — campo correcto confirmado**: el campo es `per_carnet` (con 'r'),
  no `per_canet`. Corregido en el flujo de datos del Contexto.
- **Fase 2 — PKs confirmados como INT**: todos los campos `_codigo` son INT
  en SQL Server. No se usa `$keyType = 'string'`.
-->
