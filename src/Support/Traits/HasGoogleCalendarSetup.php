<?php

namespace Innoboxrr\GoogleCalendar\Support\Traits;

use Innoboxrr\GoogleCalendar\Models\GoogleCalendarSetup;

trait HasGoogleCalendarSetup
{
    public function setup()
    {
        return $this->hasOne(GoogleCalendarSetup::class);
    }
}