# Changelog

Todas las modificaciones notables a este proyecto serán documentadas en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto sigue [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-11-27

Rompe con la 0.0.1: en una versión 0.x es la menor la que lo indica. Exige
PHP 8.3 y Laravel 13, y `createEvent()`/`updateEvent()` lanzan cuando Google
responde con error.

### Cambiado

- **Laravel 13 y PHP 8.3.** Se declaran `php ^8.3` e `illuminate/database`,
  `illuminate/http` e `illuminate/support` `^13.0`. Antes no declaraba ni PHP ni
  Laravel, y se instalaba en cualquier versión aunque no funcionara.
- **Se desarrolla con `larapack-generator` 9** (`require-dev`, `^9.0`), con
  `orchestra/testbench ^11.0` y PHPUnit 12. No viaja a la aplicación.
- **Los proveedores heredan de `ServiceProvider`**, no de los de Foundation, y
  no leen la caché al arrancar. La clave de caché `auth_policies` y
  `events_and_observers` era la misma en todos los paquetes de esta
  generación, `php artisan migrate` fallaba en una aplicación nueva con
  `CACHE_STORE=database` antes de crear la tabla `cache`, las rutas de la
  aplicación se cargaban otra vez por paquete y el correo de verificación se
  registraba repetido.
- **`createEvent()` y `updateEvent()` lanzan `RequestException`** cuando Google
  responde con error, en vez de pasar el error al DTO como si fuera un evento.

### Corregido

- **Crear o actualizar un evento no funcionaba.** `EventRequest::toArray()`
  mandaba `start` y `end` como cadena, y la API exige
  `{"dateTime": ...}`: Google contestaba 400. Ahora manda ese formato y sólo
  los campos que se pueden escribir.
- **`EventRequest` exigía campos de respuesta** (`htmlLink`, `created`,
  `updated`, `location`, `status`, `description`): no se podía armar una
  petición sin inventarlos. Ahora son opcionales, y `start`/`end` aceptan una
  cadena RFC 3339 o el arreglo de Google.
- **`EventResponse` tronaba** con un evento sin descripción, sin título o sin
  lugar (Google omite los vacíos) y con uno de día completo (`date` en vez de
  `dateTime`).
- **Un usuario podía operar con el token de otro.** `CalendarService` era
  singleton, se construye con el token del usuario autenticado y la fachada
  guardaba la instancia: en un worker de colas o en Octane, el segundo usuario
  usaba el calendario del primero. Ahora se construye en cada uso.
- **`vendor:publish --tag=google-calendar-config`**, la orden del README, no
  publicaba nada: la etiqueta era `config`. Ahora publica con las dos.
- **El README describía una API que no existe** (`Auth::redirect($user)`,
  `Calendar::for($user)`, otras variables de entorno). Se reescribe con la
  real, y dice que la ruta `google/callback` la define la aplicación.
- `vendor/autoload.php` y `vendor/composer/*` estaban versionados pese al
  `.gitignore`; se sacan del repositorio.

### Añadido

- Suite de pruebas: el paquete arranca en Laravel 13, la migración corre y se
  revierte, y las operaciones del calendario con la API de Google simulada
  (cuerpo de la petición, token por usuario, renovación del token vencido,
  cuenta sin conectar, errores de Google, eventos de día completo).
- CI con los flujos reutilizables de `innoboxrr/.github`: pruebas y
  `larapack:audit` en cada push y pull request, y etiqueta a partir de
  `VERSION` cuando pasan.
