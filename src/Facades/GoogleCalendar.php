<?php

namespace Innoboxrr\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;
use Innoboxrr\GoogleCalendar\Services\GoogleCalendarService;

class GoogleCalendar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GoogleCalendarService::class;
    }
}