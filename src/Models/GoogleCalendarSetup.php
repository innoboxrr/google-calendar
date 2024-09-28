<?php

namespace Innoboxrr\GoogleCalendar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Innoboxrr\GoogleCalendar\Facades\Auth as GoogleAuth;
use Innoboxrr\GoogleCalendar\Exceptions\NotSetupException;
use Innoboxrr\GoogleCalendar\Exceptions\UnsetTokenException;

class GoogleCalendarSetup extends Model
{

    use HasFactory,
        SoftDeletes;
        
    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_at',
        'user_id',
    ];

    protected static function newFactory()
    {
        return \Innoboxrr\GoogleCalendar\Database\Factories\GoogleCalendarSetupFactory::new();
    }

    public static function callback($code)
    {
        $response = GoogleAuth::authCallback($code);
        if(isset($response['access_token'])) {
            Auth::user()->setup()->updateOrCreate([], [
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'],
                'expires_at' => now()->addSeconds($response['expires_in']),
            ]);
            return redirect()->to(config('google-calendar.callback_success_url'));
        }
        return redirect()->to(config('google-calendar.callback_failure_url'));
    }

    public static function getAuthToken()
    {
        $setup = self::where('user_id', Auth::id())->first();

        if(!$setup) {
            throw new NotSetupException();
        }

        if(!$setup->access_token) {
            throw UnsetTokenException::getToken();
        }

        if($setup->expires_at < now()) {
            return self::refreshToken($setup);
        }

        return $setup->access_token;
    }

    public static function refreshToken($setup): string
    {
        if(!$setup->refresh_token) {
            throw UnsetTokenException::refreshToken();
        }

        $response = GoogleAuth::refreshToken($setup->refresh_token);

        if(isset($response['error']) || !isset($response['access_token'])) {
            throw UnsetTokenException::refreshToken();
        }

        $setup->access_token = $response['access_token'];
        $setup->expires_at = now()->addSeconds($response['expires_in']);
        $setup->save();

        return $setup->access_token;
    }

}
