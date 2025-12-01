<?php

namespace App\Services\Astronomy;

/**
 * Geographic coordinates (latitude and longitude)
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class Coordinates
{
    public function __construct(
        public float $latitude,
        public float $longitude
    ) {}

    /**
     * Create coordinates from an array
     */
    public static function fromArray(array $data): self
    {
        return new self($data['latitude'] ?? 0, $data['longitude'] ?? 0);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }
}
