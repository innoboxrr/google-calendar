<?php

namespace Innoboxrr\GoogleCalendar\Services;

use  Innoboxrr\GoogleCalendar\Models\GoogleCalendarSetup;

abstract class TokenService
{
    protected ?string $accessToken;

    public function __construct()
    {
        $this->accessToken = $this->getAccessToken();
    }

    protected function getAccessToken(): ?string
    {
        return GoogleCalendarSetup::getAuthToken();
    }
}