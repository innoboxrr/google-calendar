<?php

namespace Innoboxrr\GoogleCalendar\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Innoboxrr\GoogleCalendar\DTOs\EventRequest;
use Innoboxrr\GoogleCalendar\DTOs\EventResponse;
use Innoboxrr\GoogleCalendar\Exceptions\NotSetupException;
use Innoboxrr\GoogleCalendar\Facades\Calendar;
use Innoboxrr\GoogleCalendar\Models\GoogleCalendarSetup;
use Innoboxrr\GoogleCalendar\Tests\Fixtures\User;
use Innoboxrr\GoogleCalendar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * El calendario de un usuario conectado, con la API de Google simulada.
 */
final class CalendarTest extends TestCase
{
    private const EVENTS = 'https://www.googleapis.com/calendar/v3/calendars/primary/events';

    protected function defineEnvironment($app): void
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('google-calendar.client_id', 'client-id');
        $app['config']->set('google-calendar.client_secret', 'client-secret');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->artisan('migrate')->run();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    #[Test]
    public function la_migracion_crea_la_tabla_y_se_revierte(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('google_calendar_setups'));

        $this->artisan('migrate:rollback')->assertSuccessful();

        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('google_calendar_setups'));
    }

    /**
     * Antes `start` y `end` viajaban como cadena y Google respondia 400.
     */
    #[Test]
    public function crear_un_evento_manda_el_cuerpo_que_espera_google(): void
    {
        $this->connect($this->user('ana'), 'token-ana');

        Http::fake([self::EVENTS => Http::response($this->googleEvent())]);

        $response = Calendar::createEvent(EventRequest::fromArray([
            'summary' => 'Consulta',
            'start' => '2026-12-01T10:00:00-06:00',
            'end' => '2026-12-01T11:00:00-06:00',
        ]));

        $this->assertInstanceOf(EventResponse::class, $response);
        $this->assertSame('evt-1', $response->id);

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === self::EVENTS
            && $request->hasHeader('Authorization', 'Bearer token-ana')
            && $request->data() === [
                'summary' => 'Consulta',
                'start' => ['dateTime' => '2026-12-01T10:00:00-06:00'],
                'end' => ['dateTime' => '2026-12-01T11:00:00-06:00'],
            ]);
    }

    /**
     * Antes CalendarService era singleton y la fachada guardaba la instancia:
     * en un mismo proceso, el segundo usuario usaba el token del primero.
     */
    #[Test]
    public function cada_usuario_opera_con_su_propio_token(): void
    {
        $ana = $this->user('ana');
        $luis = $this->user('luis');
        $this->connect($ana, 'token-ana');
        $this->connect($luis, 'token-luis');

        Http::fake([self::EVENTS => Http::response(['items' => []])]);

        $this->actingAs($ana);
        Calendar::getEvents();

        $this->actingAs($luis);
        Calendar::getEvents();

        $tokens = Http::recorded()->map(fn ($pair) => $pair[0]->header('Authorization')[0])->all();

        $this->assertSame(['Bearer token-ana', 'Bearer token-luis'], $tokens);
    }

    #[Test]
    public function un_token_vencido_se_renueva(): void
    {
        $user = $this->user('ana');
        $this->connect($user, 'viejo', now()->subMinute());

        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'nuevo', 'expires_in' => 3600]),
            self::EVENTS => Http::response(['items' => []]),
        ]);

        Calendar::getEvents();

        Http::assertSent(fn (Request $request) => $request->url() === self::EVENTS
            && $request->hasHeader('Authorization', 'Bearer nuevo'));
        $this->assertSame('nuevo', GoogleCalendarSetup::where('user_id', $user->id)->value('access_token'));
    }

    #[Test]
    public function sin_conectar_la_cuenta_lanza_not_setup(): void
    {
        $this->actingAs($this->user('ana'));

        $this->expectException(NotSetupException::class);

        Calendar::getEvents();
    }

    #[Test]
    public function un_error_de_google_lanza_en_vez_de_armar_un_evento_vacio(): void
    {
        $this->connect($this->user('ana'), 'token-ana');

        Http::fake([self::EVENTS => Http::response(['error' => ['code' => 400]], 400)]);

        $this->expectException(RequestException::class);

        Calendar::createEvent(EventRequest::fromArray([
            'summary' => 'Consulta',
            'start' => '2026-12-01T10:00:00-06:00',
            'end' => '2026-12-01T11:00:00-06:00',
        ]));
    }

    /**
     * Google omite los campos vacios y un evento de dia completo trae `date`.
     */
    #[Test]
    public function la_respuesta_acepta_un_evento_de_dia_completo_sin_descripcion(): void
    {
        $response = EventResponse::fromArray([
            'id' => 'evt-2',
            'status' => 'confirmed',
            'start' => ['date' => '2026-12-24'],
            'end' => ['date' => '2026-12-25'],
        ]);

        $this->assertSame('2026-12-24', $response->start);
        $this->assertSame('', $response->description);
    }

    private function user(string $name): User
    {
        return User::forceCreate([
            'name' => $name,
            'email' => "{$name}@example.test",
            'password' => 'secret',
        ]);
    }

    private function connect(User $user, string $token, $expiresAt = null): void
    {
        $user->setup()->create([
            'access_token' => $token,
            'refresh_token' => 'refresh-' . $user->name,
            'expires_at' => $expiresAt ?? now()->addHour(),
        ]);

        $this->actingAs($user);
    }

    /**
     * @return array<string, mixed>
     */
    private function googleEvent(): array
    {
        return [
            'id' => 'evt-1',
            'status' => 'confirmed',
            'htmlLink' => 'https://www.google.com/calendar/event?eid=evt-1',
            'created' => '2026-11-27T10:00:00.000Z',
            'updated' => '2026-11-27T10:00:00.000Z',
            'summary' => 'Consulta',
            'start' => ['dateTime' => '2026-12-01T10:00:00-06:00'],
            'end' => ['dateTime' => '2026-12-01T11:00:00-06:00'],
        ];
    }
}
