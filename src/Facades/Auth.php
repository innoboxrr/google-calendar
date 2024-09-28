<?php

namespace Innoboxrr\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;
use Innoboxrr\GoogleCalendar\Services\AuthService;

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AuthService::class;
    }
}