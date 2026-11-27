<?php

namespace Innoboxrr\GoogleCalendar\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Innoboxrr\GoogleCalendar\Support\Traits\HasGoogleCalendarSetup;

/**
 * El usuario de la aplicacion anfitriona, con el trait que pide el paquete.
 */
class User extends Authenticatable
{
    use HasGoogleCalendarSetup;

    protected $table = 'users';

    protected $guarded = [];
}
