# Google Calendar

**Google Calendar in Laravel, with per-user OAuth and typed events.**

Two facades, typed DTOs, and explicit exceptions when a user has not connected their account yet — so calendar integration stops being a pile of array juggling and silent failures.

```php
use Innoboxrr\GoogleCalendar\DTOs\EventRequest;
use Innoboxrr\GoogleCalendar\Facades\Auth;
use Innoboxrr\GoogleCalendar\Facades\Calendar;

return Auth::authRedirect();        // send the signed-in user through the consent screen

Calendar::createEvent(EventRequest::fromArray([
    'summary' => 'Consultation',
    'start'   => $appointment->starts_at->toRfc3339String(),
    'end'     => $appointment->ends_at->toRfc3339String(),
]));
```

`Calendar` always acts on behalf of the **authenticated** user (`Auth::id()`), on their `primary` calendar.

## What ships

| | |
|---|---|
| `Facades\Auth` | OAuth flow — `authRedirect()`, `authCallback($code)`, `refreshToken($token)` |
| `Facades\Calendar` | `getEvents()`, `createEvent()`, `updateEvent()`, `deleteEvent($id)` on the user's primary calendar |
| `DTOs\EventRequest` / `EventResponse` | Typed in, typed out — `fromArray()` / `toArray()` |
| `Contracts\EventInterface` | The four calendar operations, if you want to swap the implementation |
| `Models\GoogleCalendarSetup` | Per-user tokens; `callback($code)` stores them, `getAuthToken()` refreshes them when expired |
| `Support\Traits\HasGoogleCalendarSetup` | Add it to your `User` model: gives `$user->setup()` |
| `Exceptions` | `NotSetupException` and `UnsetTokenException` — explicit, catchable failures |

**Per-user, not per-app.** Each user connects their own calendar. The setup model tracks who is connected, and the exceptions tell you plainly when someone is not. An error answer from Google throws `Illuminate\Http\Client\RequestException`.

## Install

Requires PHP 8.3 and Laravel 13.

```bash
composer require innoboxrr/google-calendar
php artisan vendor:publish --tag=google-calendar-config
php artisan migrate
```

```dotenv
GOOGLE_CLIENT_ID=...
GOOGLE_SECRET=...
GOOGLE_CALLBACK_SUCCESS_URL=/calendar/connected
GOOGLE_CALLBACK_FAILURE_URL=/calendar/failed
```

The package does **not** register the OAuth callback route. Google redirects to `url('google/callback')`, so your application defines it:

```php
use Innoboxrr\GoogleCalendar\Models\GoogleCalendarSetup;

Route::middleware('auth')->get('google/callback', fn (Request $request) => GoogleCalendarSetup::callback($request->query('code')));
```

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
