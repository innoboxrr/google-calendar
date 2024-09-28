<?php

namespace Innoboxrr\GoogleCalendar\Contracts;

use Innoboxrr\GoogleCalendar\DTOs\Event;
use Innoboxrr\GoogleCalendar\DTOs\EventResponse;

interface EventInterface
{
    public function getEvents(): array;
    
    public function createEvent(Event $event): EventResponse;
    
    public function updateEvent(Event $event): EventResponse;

    public function deleteEvent($id): bool;
}