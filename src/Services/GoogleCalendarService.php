<?php

namespace Innoboxrr\GoogleCalendar\Services;

class GoogleCalendarService
{

    protected $client;

    protected $secret;

    public function __construct()
    {
        $this->client = config('google-calendar.client_id');
        $this->secret = config('google-calendar.client_secret');
    }

    public function authRedirect()
    {
        dd($this);
    }

    public function authCallback()
    {
        // Google auth callback
    }

    public function getEvents()
    {
        // Get events
    }
}