<?php

namespace Innoboxrr\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;
use Innoboxrr\GoogleCalendar\Services\CalendarService;

class Calendar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CalendarService::class;
    }
}