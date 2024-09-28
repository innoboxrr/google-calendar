<?php

namespace Innoboxrr\GoogleCalendar\Contracts;

use Innoboxrr\GoogleCalendar\DTOs\EventRequest;
use Innoboxrr\GoogleCalendar\DTOs\EventResponse;

interface EventInterface
{
    public function getEvents(): array;
    
    public function createEvent(EventRequest $event): EventResponse;
    
    public function updateEvent(EventRequest $event): EventResponse;

    public function deleteEvent($id): bool;
}