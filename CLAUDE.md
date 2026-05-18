<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>

# Descripcion:

Sistema de control de proceso de creacion de contenido, basado en la metodologia de 4 pasos para documentar trabajo cotidiano:
  - Capturar
  - Procesar
  - Transformar
  - Publicar
Este sistema se enfocara en la etapa 2: Procesar y parte de la etapa 3: transformar. Permitiendo asi tener almacenadas de forma ordenada las notas, su procesamiento, hasta la conversion a scripts para reels o post para linkedin o blog inicialmente aunque se pueden considerar el almacenamiento para otro tipo de formatos. Esto permitira obtener metricas y hacer analisis a lo largo del tiempo de la evolucion de este proceso de creacion de contenido.

El punto de partida en cuanto a herramientas a utilizar por fase son:
Captura:
  • Google Keep (en sistema este metodo sera remplazado por telegram bot)
  • OBS (grabar pantalla)
  • Snipping tool (recortes y capturas de pantalla)

Procesamiento:
  • Para ordenar el contenido se usa actualmente onenote y se usara el sistema cuando este desarrollado.
  • Procesadores de texto como block de notas, word

Transformacion:
  • Video: capcut
  • IA (chatgpt o claude): Apoyo y revision de scripts, posts y blogs

Publicacion:
  • Plataforma especifica segun tipo de contenido

Objetivos:
  Objetivo principal: Almacenar la informacion de la fase 2 y fase 3 para poder dar seguimieto y mejorar de manera iterativa el proceso de creacion de contenido automatizando sistematicamente las tareas repetitivas que segun la experiencia esten causando mayor friccion.
  
  Objetivos especificos:
    ○ Almacenar la notas provenientes de keep u otro medio
    ○ Almacenar las rutas donde se encuentra el contenido multimedia
    ○ Categorizar la nota segun el tipo de contenido y el medio a publicar
    ○ Desarrollar la idea (escribir el texto extendido proveniente de la nota)
    ○ Visualizar el estado de la nota (desechadas, pendiente, procesando, finalizado, publicado)
    ○ Dashboard (metricas relevantes: Cantidad de notas totales y por estado, cantidad de contenido publicado por canal, frecuencia promedio de publicacion).

## Casos de uso:

Caso de uso: 
Actor principal: 
Descripcion: 
Pre-condiciones: 
Flujo basico:
Flujo alternativo: 
Post-condiciones:

### Caso de uso: Gestión de notas
Actor principal: Usuario
Descripcion: El usuario puede Crear una nota en el sistema o entrar a una importada desde el bot de telegram, con las notas el usuario puede modificarlas, desecharlas o categorizarlas por insight, decision o historia.
Pre-condiciones: 
  • Estar logeado como usuario (basico)
Flujo basico: Categorizar nota
  • Seleccionar una nota
  • Revisar nota y determinar si esa idea puede ser util para la audiencia
  • Categorizarla por insight, decision o historia
Flujo alternativo: Crear nota
  • Dar clic a crear nota
  • Escribir la nota
  • Guardar
Flujo alternativo: Modificar nota
  • Seleccionar una nota creado o importada
  • Modificar su contenido
  • Guardar modificacion
Flujo alternativo: Desechar nota
  • Seleccionar nota
  • Revisar nota y determinar si la nota no tiene contenido relevante para la audiencia
  • Desechar
Post-condiciones:

### Caso de uso: Gestion del script
Actor principal: Usuario (basico)
Descripcion: Despues de categorizar una nota el usuario debe desarrollarla idea base sobre esa nota, determinar objetivo principal (que es lo desea transmitir con ese contenido), la estructura que tiene en mente y el hilo narrativo del script.
Pre-condiciones: 
  • Haber categorizado la nota
Flujo basico: Desarrollo base de script
  • Seleccionar la nota
  • Seleccionar el canal para el cual estas preparando el script
  • Escribir el script inicial segun las instrucciones propuestas
  • Guardar el script
Flujo alternativo: Edicion y revision con IA
  • Copiar el script y pasarla a un modelo LLM (ChatGPT, Claude, Gemini)
  • Darle el contexto y las indicaciones (Aca pudiese desarrollar una skill para este trabajo)
  • Obtener el resultado
  • Iterar si es necesario
Flujo alternativo: Edicion y revision del script generado por IA
  • Leer el script generado por la IA
  • Editar las partes que no resuenan con tu tono y mensaje
  • Guardar el script ya revisado por ti
Flujo alternativo: Archivar nota
  • Seleccionar una nota
  • Dar clic al boton archivar
Post-condiciones:
  • Si es necesario, apoyarse con la IA para enriquecer y mejorar la estructura del script

## Arquitectura y decisiones tecnicas:

Lenguaje y Framework 
  • Lenguaje: PHP 8.5
  • Framework: Laravel 13
  • Librerias: Fortify, Livewire, spatie, datatable, sweetAlert.
  • Arquitectura: En capas (Layered Architecture)

Descripción general de la arquitectura
El sistema está construido bajo una arquitectura en capas, con el objetivo de lograr separación de responsabilidades, facilidad de mantenimiento, y extensibilidad del código.
Cada capa cumple una función específica dentro del flujo de una petición, permitiendo que los cambios en una parte del sistema no afecten otras.
El flujo general es el siguiente:

Usuario → Controller → Model → Base de Datos

## Estructura de carpetas

├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── NoteController.php
│   │   │   ├── ScriptController.php
│   │   │   ├── ExternalRevisionController.php
│   │   │   ├── PublishLogController.php
│   │   │   └── DashboardController.php
│   │   ├── Middleware
│   │   │   └── EnsureNoteOwner.php
│   │   └── Requests
│   │       ├── StoreNoteRequest.php
│   │       ├── UpdateNoteRequest.php
│   │       ├── StoreScriptRequest.php
│   │       ├── StoreExternalRevisionRequest.php
│   │       └── StorePublishLogRequest.php
│   ├── Models
│   │   ├── User.php
│   │   ├── Note.php
│   │   ├── NoteAttachment.php
│   │   ├── NoteScript.php
│   │   ├── ExternalRevision.php
│   │   ├── PublishLog.php
│   │   └── Channel.php
│   ├── Telegram
│   │   └── Handlers
│   │       ├── InitNoteCommandHandler.php
│   │       └── FinishNoteCommandHandler.php
│   ├── Policies
│   │   ├── NotePolicy.php
│   │   └── ScriptPolicy.php
│   └── Providers
│       └── AppServiceProvider.php
├── bootstrap
├── config
│   └── telegram.php
├── database
│   ├── migrations
│   └── seeders
│       └── RolesAndPermissionsSeeder.php
├── public
├── resources
│   └── views
│       ├── components
│       │   ├── note-list.blade.php
│       │   ├── note-form.blade.php
│       │   ├── note-detail.blade.php
│       │   ├── script-editor.blade.php
│       │   ├── external-revision-form.blade.php
│       │   └── dashboard-metrics.blade.php
│       ├── pages
│       │   ├── notes-index.blade.php
│       │   ├── notes-create.blade.php
│       │   ├── notes-show.blade.php
│       │   ├── script-create.blade.php
│       │   ├── script-show.blade.php
│       │   └── dashboard.blade.php
│       └── layouts
│           └── app.blade.php
├── routes
│   ├── web.php
│   ├── api.php
│   └── telegram.php
├── storage
│   └── app
│       └── notes
│           ├── voice
│           ├── images
│           └── documents
└── tests
    └── Feature
        ├── NoteTest.php
        ├── ScriptTest.php
        └── TelegramWebhookTest.php

Users
- id (PK)
- name
- email
- email_verified_at
- password
- timestamps

Notes
- id (PK)
- user_id (FK → Users)
- channel_id (FK → Channels)
- state_id (FK → States)
- content
- timestamps

Channels
- id (PK)
- name
- description

States
- id (PK)
- name
- description

Scripts
- id (PK)
- note_id (FK → Notes)
- classification_id (FK → Classifications)
- state_id (FK → States)
- content
- timestamps

Classifications
- id (PK)
- name
- description
- timestamps
