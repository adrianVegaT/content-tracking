# Plan: telegram bot

## Contexto

La idea general de esta funcionalidad es que el sistema almacene notas a traves de un bot de telegram, para ello vamos a utilizar la libreria https://nutgram.dev/, el usuario podra escribir en el bot y el sistema reconocera ese envio y la almacenara como una nota, (revisa el CLAUDE.md para entender mejor el funcionamiento del sistema y la estructura propuesta). 

Esta funcion debe de poder recibir notas de voz las cuales debe conventir a texto, y claramente los mensajes de texto.
El envio de mensajes estara delimitado por comandos, /init y /finish para determinar el inicio y final de una nota aunque venga de varios mensajes.

Estos mensajes se deben almacenar por usuario, en una estructura de tabla con estos campos: id, user_id, channel_id, state_id, content, timestamps.


## Arquitectura

- Librería bot: `nutgram/laravel` (Nutgram registra el endpoint webhook automáticamente — no se usa un controller dedicado).
- Handlers del bot en `app/Telegram/Handlers/` (convención Nutgram).
- Rutas del bot definidas en `routes/telegram.php` (archivo de handlers Nutgram, no rutas Laravel).
- Configuración en `config/nutgram.php` publicado por el paquete.
- Conversión voz→texto diferida a Fase 2 (depende del modelo `Note`).

## Fases con tareas atomicas

### Fase 1: Instalacion y configuracion de libreria nutgram
- [x] Instalacion de paquetes (`composer require nutgram/laravel` + publicar config)
- [x] Configuracion de webhook (`.env` con `TELEGRAM_TOKEN`, exención CSRF en `bootstrap/app.php`, `php artisan nutgram:hook:set`)
- [x] Creacion de rutas (`routes/telegram.php` con handlers Nutgram registrados)
- [x] Configurar los comandos de inicio y cierre de notas (`InitNoteCommandHandler`, `FinishNoteCommandHandler` en `app/Telegram/Handlers/` — solo responden texto de confirmación, sin persistencia)
- [x] Eliminar `TelegramWebhookController.php` de la estructura en `CLAUDE.md`
- [x] Tests en `tests/Feature/TelegramWebhookTest.php` con Nutgram `fake()`

### Fase 2: Infraestructura de BD (migraciones, modelos, seeders, factories)

**Migraciones** (en este orden por dependencias FK):

`create_channels_table`
```
id, name (string unique), description (text nullable), timestamps
```
`create_states_table`
```
id, name (string unique), description (text nullable), timestamps
```
`create_classifications_table`
```
id, name (string unique), description (text nullable), timestamps
```
`create_telegram_users_table`
```
id, user_id (FK→users cascadeOnDelete), telegram_chat_id (bigInteger unique),
telegram_username (string nullable), timestamps
```
`create_notes_table`
```
id, user_id (FK→users cascadeOnDelete), channel_id (FK→channels restrictOnDelete),
state_id (FK→states restrictOnDelete), content (longText nullable), timestamps
```

**Seeders de referencia:**
- `ChannelSeeder`: telegram, web
- `StateSeeder`: capturing, pending, classified, in_progress, done, published, discarded
- `ClassificationSeeder`: insight, decision, historia
- `DatabaseSeeder` actualizado: ChannelSeeder → StateSeeder → ClassificationSeeder

**Modelos** (todos con `#[Fillable([...])]`): Channel, State, Classification, TelegramUser, Note
**Factories**: ChannelFactory (estado `telegram()`), StateFactory (estados `capturing()`, `pending()`), TelegramUserFactory, NoteFactory (estados `capturing()`, `pending()`)
**User** modificado: agregar relaciones `hasMany Notes` y `hasOne TelegramUser`

- [x] Crear migraciones en el orden indicado
- [x] Crear modelos con atributos y relaciones
- [x] Crear factories con estados personalizados
- [x] Crear seeders y actualizar DatabaseSeeder
- [x] Ejecutar `php artisan migrate` y `php artisan db:seed`

### Fase 3: Servicio de negocio (NoteService)

Clase `app/Services/NoteService.php` con cuatro métodos:

| Método | Responsabilidad |
|---|---|
| `resolveUserFromChatId(int $chatId): User` | Resuelve el User desde el chat_id. Lanza `ModelNotFoundException` si no hay mapeo. |
| `openDraftNote(User $user): Note` | Crea o retorna la nota con estado `capturing`. Idempotente via `firstOrCreate`. |
| `appendTextToDraft(User $user, string $text): ?Note` | Concatena texto con `\n`. Retorna `null` si no hay nota activa. |
| `finishDraftNote(User $user): ?Note` | Cambia `capturing` → `pending`. Retorna `null` si no hay nota activa. |

- [x] Crear `app/Services/NoteService.php` con los cuatro métodos

### Fase 4: Handlers y rutas del bot (mensajes de texto)

Actualizar `/init` y `/finish` para usar `NoteService`. Crear `TextMessageHandler` para mensajes libres.

Mensajes de error:
- Chat ID no vinculado → "Tu cuenta de Telegram no está vinculada."
- Texto sin nota activa → "No tienes una nota activa. Usa /init para comenzar."
- `/finish` sin nota activa → "No tienes una nota activa para cerrar."

Ruta nueva en `routes/telegram.php`:
```php
$bot->onText('.*', TextMessageHandler::class);
```

- [x] Actualizar `InitNoteCommandHandler` (usa `openDraftNote`)
- [x] Actualizar `FinishNoteCommandHandler` (usa `finishDraftNote`)
- [x] Crear `TextMessageHandler` (usa `appendTextToDraft`)
- [x] Registrar `onText` en `routes/telegram.php`

### Fase 5: Voz a texto (OpenAI Whisper API)

Servicio: OpenAI Whisper API ($0.006/min — ~$0.06/mes para 10 min de audio).
Descartado Whisper local: VPS de producción es 1 CPU, 4 GB RAM, sin GPU.

Flujo:
1. Usuario envía audio `.ogg` con nota activa
2. `VoiceMessageHandler` descarga el archivo desde Telegram al storage temporal
3. `TranscriptionService` lo envía a OpenAI (`/v1/audio/transcriptions`, modelo `whisper-1`, `language=es`)
4. El texto transcrito se pasa a `NoteService::appendTextToDraft`
5. Se elimina el archivo temporal

- [x] Crear `app/Services/TranscriptionService.php`
- [x] Crear `app/Telegram/Handlers/VoiceMessageHandler.php`
- [x] Registrar `$bot->onVoice(VoiceMessageHandler::class)` en `routes/telegram.php`
- [x] Agregar `OPENAI_API_KEY` a `.env` y `.env.example`

### Fase 6: Tests

**`NoteServiceTest.php`** (nuevo):
- Abre nota capturing para el usuario
- No duplica la nota capturing si ya existe (idempotencia)
- Concatena texto con `\n` al capturing activo
- Retorna `null` al agregar texto sin nota activa
- Cierra capturing → pending
- Retorna `null` al cerrar sin nota activa
- Resuelve usuario desde chat_id
- Lanza `ModelNotFoundException` para chat_id desconocido

**`TelegramWebhookTest.php`** (actualizar + agregar):
- `/init` crea nota con estado `capturing`
- `/init` es idempotente con nota activa existente
- Texto sin nota activa → mensaje de error
- Texto con nota activa → concatena contenido
- `/finish` sin nota activa → mensaje de error
- `/finish` con nota activa → pasa a `pending`
- Chat ID no vinculado → mensaje de error en `/init`, `/finish` y texto libre
- Nota de voz transcrita y concatenada al capturing activo

- [~] Crear `tests/Feature/NoteServiceTest.php`
- [~] Actualizar `tests/Feature/TelegramWebhookTest.php`
- [~] Ejecutar `php artisan test --compact` — todos los tests deben pasar

## Fuera de alcance:
- Interfaz web para gestión de notas (plan separado)
- Clasificación y desarrollo de scripts (plan separado)
- Publicación de contenido (plan separado)

## Decisiones tomadas

- `TelegramWebhookController` omitido: Nutgram registra el endpoint automáticamente.
- Handlers en `app/Telegram/Handlers/`, no inline en el archivo de rutas.
- Fase 1 sin persistencia: los comandos `/init` y `/finish` solo responden texto de confirmación.
- Conversión voz→texto queda para Fase 2 (necesita el modelo `Note`).


