<?php

namespace Innoboxrr\GoogleCalendar\Services;

use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{

    protected string $client;
    protected string $secret;
    protected string $redirectUrl;

    public function __construct()
    {
        $this->client = config('google-calendar.client_id');
        $this->secret = config('google-calendar.client_secret'); 
        $this->redirectUrl = url('google/callback');
    }

    private function getoAuthUrl ()
    {
        return "https://accounts.google.com/o/oauth2/v2/auth?client_id={$this->client}&redirect_uri={$this->redirectUrl}&response_type=code&scope=https://www.googleapis.com/auth/calendar&access_type=offline&prompt=consent";
    }

    public function authRedirect()
    {
        return redirect($this->getoAuthUrl());
    }

    public function authCallback($code)
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $this->client,
            'client_secret' => $this->secret,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code'
        ]);

        return $response->json();
    }

    public function refreshToken($refreshToken)
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'refresh_token' => $refreshToken,
            'client_id' => $this->client,
            'client_secret' => $this->secret,
            'grant_type' => 'refresh_token'
        ]);

        return $response->json();
    }

    public function getEvents()
    {   
        // HARD CODED TOKEN
        $token = "ya29.a0AcM612wBED0sAtuGpFoJdg53_DjenPb6Vh5Q3HCwfiZELAk-h9RiVf8D-_VkKEfzUVpIWYVfVeH4oSK9-ILV1iK7PNIiUm2TPmrDa3bn5IkV6fs3IX2SemnUl_yf7AoN-ePFwXZYj2IYhuOrV8xMwQ_yWxjoIuLOYGd-xkQMaCgYKAVcSARESFQHGX2MiUQcm3jGX2MWAnBbHZjo6zw0175";

        return Http::withToken($token)
            ->get('https://www.googleapis.com/calendar/v3/calendars/primary/events')
            ->json();
    }

    public function createEvent($data)
    {
        //
    }

    public function updateEvent($data)
    {
        //
    }

    public function deleteEvent($data)
    {
        //
    }
}