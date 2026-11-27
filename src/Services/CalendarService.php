<?php

namespace Innoboxrr\GoogleCalendar\Services;

use Innoboxrr\GoogleCalendar\Contracts\EventInterface;
use Illuminate\Support\Facades\Http;
use Innoboxrr\GoogleCalendar\DTOs\EventRequest;
use Innoboxrr\GoogleCalendar\DTOs\EventResponse;

/**
 * Se construye con el token del usuario autenticado. Por eso no es singleton
 * ni la fachada guarda la instancia: en un worker de colas o en Octane el
 * segundo usuario recibia la instancia del primero, con su token.
 *
 * Una respuesta de error de Google lanza RequestException en vez de llegar al
 * DTO como si fuera un evento.
 */
class CalendarService extends TokenService implements EventInterface
{

    public function getEvents(): array
    {   
        return Http::withToken($this->accessToken)
            ->get('https://www.googleapis.com/calendar/v3/calendars/primary/events')
            ->json();
    }

    public function createEvent(EventRequest $event): EventResponse
    {
        return EventResponse::fromArray(
            Http::withToken($this->accessToken)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $event->toArray())
                ->throw()
                ->json()
        );
    }

    public function updateEvent(EventRequest $event): EventResponse
    {
        return EventResponse::fromArray(
            Http::withToken($this->accessToken)
                ->put("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$event->id}", $event->toArray())
                ->throw()
                ->json()
        );
    }

    public function deleteEvent($id): bool
    {
        return Http::withToken($this->accessToken)
            ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$id}")
            ->successful();
    }
}