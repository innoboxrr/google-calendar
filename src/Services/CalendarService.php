<?php

namespace Innoboxrr\GoogleCalendar\Services;

use Innoboxrr\GoogleCalendar\Contracts\EventInterface;
use Illuminate\Support\Facades\Http;
use Innoboxrr\GoogleCalendar\DTOs\Event;
use Innoboxrr\GoogleCalendar\DTOs\EventResponse;

class CalendarService extends TokenService implements EventInterface
{

    public function getEvents(): array
    {   
        return Http::withToken($this->accessToken)
            ->get('https://www.googleapis.com/calendar/v3/calendars/primary/events')
            ->json();
    }

    public function createEvent(Event $event): EventResponse
    {
        return EventResponse::fromArray(
            Http::withToken($this->accessToken)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $event->toArray())
                ->json()
        );
    }

    public function updateEvent(Event $event): EventResponse
    {
        return EventResponse::fromArray(
            Http::withToken($this->accessToken)
                ->put("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$event->id}", $event->toArray())
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