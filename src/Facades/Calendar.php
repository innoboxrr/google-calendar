<?php

namespace Innoboxrr\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;
use Innoboxrr\GoogleCalendar\Services\CalendarService;

class Calendar extends Facade
{
    /**
     * Sin cachear la instancia: CalendarService guarda el token del usuario
     * con el que se construyo, y la fachada la compartia con los siguientes.
     */
    protected static $cached = false;

    protected static function getFacadeAccessor()
    {
        return CalendarService::class;
    }
}