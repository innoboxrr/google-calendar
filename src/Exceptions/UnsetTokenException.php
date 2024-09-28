<?php

namespace Innoboxrr\GoogleCalendar\Exceptions;

use Exception;

class UnsetTokenException extends Exception
{
    public static function getToken(): self
    {
        return new self('Token not set');
    }

    public static function refreshToken(): self
    {
        return new self('Error refreshing token');
    }
}