<?php

namespace Innoboxrr\GoogleCalendar\Exceptions;

use Exception;

class NotSetupException extends Exception
{
    public function __construct($message = 'Google Calendar not setup')
    {
        parent::__construct($message);
    }
}