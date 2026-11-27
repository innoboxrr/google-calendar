<?php

namespace Innoboxrr\GoogleCalendar\DTOs;

/**
 * Lo que se manda a Google para crear o actualizar un evento.
 *
 * Acepta el mismo arreglo que devuelve la API (`start` => ['dateTime' => ...])
 * o las fechas como cadena RFC 3339. Los campos que sólo existen en la
 * respuesta (htmlLink, created, updated) son opcionales: antes eran
 * obligatorios y no se podía armar una petición sin inventarlos.
 */
class EventRequest
{
    public ?string $id;
    public string $summary;
    public string $description;
    public string $start;
    public string $end;
    public string $location;
    public string $status;
    public string $htmlLink;
    public string $created;
    public string $updated;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->summary = $data['summary'];
        $this->description = $data['description'] ?? '';
        $this->start = self::moment($data['start']);
        $this->end = self::moment($data['end']);
        $this->location = $data['location'] ?? '';
        $this->status = $data['status'] ?? '';
        $this->htmlLink = $data['htmlLink'] ?? '';
        $this->created = $data['created'] ?? '';
        $this->updated = $data['updated'] ?? '';
    }

    public static function fromArray($data): self
    {
        return new self($data);
    }

    /**
     * El cuerpo que espera la API de Calendar: `start` y `end` como objetos
     * con `dateTime`, y sólo los campos que se pueden escribir. Antes iban
     * como cadena, y Google rechazaba la petición con un 400.
     */
    public function toArray()
    {
        $data = array_filter([
            'summary' => $this->summary,
            'description' => $this->description,
            'location' => $this->location,
            'status' => $this->status,
        ], fn ($value) => $value !== '');

        $data['start'] = ['dateTime' => $this->start];
        $data['end'] = ['dateTime' => $this->end];

        if ($this->id) {
            $data['id'] = $this->id;
        }

        return $data;

    }

    private static function moment($value): string
    {
        return is_array($value) ? ($value['dateTime'] ?? $value['date']) : (string) $value;
    }
}
