<?php

namespace Innoboxrr\GoogleCalendar\Services;

use Illuminate\Support\Facades\Http;

class AuthService
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

    private function getOAuthUrl ()
    {
        return "https://accounts.google.com/o/oauth2/v2/auth?client_id={$this->client}&redirect_uri={$this->redirectUrl}&response_type=code&scope=https://www.googleapis.com/auth/calendar&access_type=offline&prompt=consent";
    }

    public function authRedirect()
    {
        return redirect($this->getOAuthUrl());
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
}