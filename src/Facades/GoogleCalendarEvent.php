<?php

namespace Innoboxrr\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;
use Innoboxrr\GoogleCalendar\Services\EventService;

class GoogleCalendarEvent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EventService::class;
    }
}