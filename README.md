# Google Calendar

**Google Calendar in Laravel, with per-user OAuth and typed events.**

Two facades, typed DTOs, and explicit exceptions when a user has not connected their account yet — so calendar integration stops being a pile of array juggling and silent failures.

```php
use Innoboxrr\GoogleCalendar\Facades\Auth;
use Innoboxrr\GoogleCalendar\Facades\Calendar;

Auth::redirect($user);              // send them through the consent screen

Calendar::for($user)->create(new EventRequest(
    summary:   'Consultation',
    startsAt:  $appointment->starts_at,
    endsAt:    $appointment->ends_at,
    attendees: [$consultee->email],
));
```

## What ships

| | |
|---|---|
| `Facades\Auth` | OAuth flow — redirect, callback, token storage and refresh |
| `Facades\Calendar` | Event operations against a connected account |
| `DTOs\EventRequest` / `EventResponse` | Typed in, typed out — no associative-array guessing |
| `Contracts\EventInterface` | Bind your own model to the event shape |
| `Models\GoogleCalendarSetup` | Per-user connection state and tokens |
| `Exceptions` | `NotSetupException` and `UnsetTokenException` — explicit, catchable failures |

**Per-user, not per-app.** Each user connects their own calendar. The setup model tracks who is connected, and the exceptions tell you plainly when someone is not — instead of a null token surfacing as a 500 three layers down.

## Install

```bash
composer require innoboxrr/google-calendar
php artisan vendor:publish --tag=google-calendar-config
php artisan migrate
```

```dotenv
GOOGLE_CALENDAR_CLIENT_ID=...
GOOGLE_CALENDAR_CLIENT_SECRET=...
GOOGLE_CALENDAR_REDIRECT_URI=...
```

---

Part of [Innobox R&R](https://github.com/innoboxrr) — 52 open-source packages extracted from production work. **[innobox.systems](https://innobox.systems)**
