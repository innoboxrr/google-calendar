<?php

namespace Innoboxrr\GoogleCalendar\DTOs;

class EventResponse
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
        // Google omite summary, description y location cuando estan vacios, y
        // un evento de dia completo trae `date` en vez de `dateTime`: antes
        // cualquiera de los dos casos tronaba con una clave indefinida.
        $this->id = $data['id'] ?? null;
        $this->summary = $data['summary'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->start = $data['start']['dateTime'] ?? $data['start']['date'];
        $this->end = $data['end']['dateTime'] ?? $data['end']['date'];
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

    public function toArray()
    {
        $data = [
            'summary' => $this->summary,
            'description' => $this->description,
            'start' => $this->start,
            'end' => $this->end,
            'location' => $this->location,
            'status' => $this->status,
            'htmlLink' => $this->htmlLink,
            'created' => $this->created,
            'updated' => $this->updated
        ];

        if ($this->id) {
            $data['id'] = $this->id;
        }

        return $data;

    }
}