<?php

namespace Innoboxrr\GoogleCalendar\Services;

abstract class TokenService
{
    protected ?string $accessToken;

    public function __construct()
    {
        $this->accessToken = $this->getAccessToken();
    }

    protected function getAccessToken(): ?string
    {
    
        return 'Happy';

    }
}