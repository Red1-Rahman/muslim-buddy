<?php

namespace App\Services\Prayer;

/**
 * Calculation parameters for prayer times
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class CalculationParameters
{
    public string $method;
    public float $fajrAngle;
    public float $ishaAngle;
    public float $ishaInterval;
    public float $maghribAngle;
    public array $methodAdjustments;
    public string $madhab;
    public string $highLatitudeRule;

    public function __construct(
        string $method = 'Other',
        float $fajrAngle = 0,
        float $ishaAngle = 0,
        float $ishaInterval = 0,
        float $maghribAngle = 0
    ) {
        $this->method = $method;
        $this->fajrAngle = $fajrAngle;
        $this->ishaAngle = $ishaAngle;
        $this->ishaInterval = $ishaInterval;
        $this->maghribAngle = $maghribAngle;
        $this->methodAdjustments = [
            'fajr' => 0,
            'sunrise' => 0,
            'dhuhr' => 0,
            'asr' => 0,
            'maghrib' => 0,
            'isha' => 0
        ];
        $this->madhab = 'Shafi'; // Shafi (shadow = 1) or Hanafi (shadow = 2)
        $this->highLatitudeRule = 'MiddleOfTheNight';
    }

    /**
     * Get shadow length based on madhab
     */
    public function shadowLength(): float
    {
        return $this->madhab === 'Hanafi' ? 2.0 : 1.0;
    }
}
