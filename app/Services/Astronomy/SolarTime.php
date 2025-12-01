<?php

namespace App\Services\Astronomy;

use DateTime;

/**
 * Solar time calculations for a specific date and location
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class SolarTime
{
    public Coordinates $observer;
    public SolarCoordinates $solar;
    public SolarCoordinates $prevSolar;
    public SolarCoordinates $nextSolar;
    public float $approxTransit;
    public float $transit;
    public float $sunrise;
    public float $sunset;

    public function __construct(DateTime $date, Coordinates $coordinates)
    {
        $julianDay = Astronomical::julianDay(
            (int)$date->format('Y'),
            (int)$date->format('m'),
            (int)$date->format('d'),
            0
        );

        $this->observer = $coordinates;
        $this->solar = new SolarCoordinates($julianDay);
        $this->prevSolar = new SolarCoordinates($julianDay - 1);
        $this->nextSolar = new SolarCoordinates($julianDay + 1);

        $m0 = Astronomical::approximateTransit(
            $coordinates->longitude,
            $this->solar->apparentSiderealTime,
            $this->solar->rightAscension
        );

        $solarAltitude = -50.0 / 60.0;

        $this->approxTransit = $m0;

        $this->transit = Astronomical::correctedTransit(
            $m0,
            $coordinates->longitude,
            $this->solar->apparentSiderealTime,
            $this->solar->rightAscension,
            $this->prevSolar->rightAscension,
            $this->nextSolar->rightAscension
        );

        $this->sunrise = Astronomical::correctedHourAngle(
            $m0,
            $solarAltitude,
            $coordinates,
            false,
            $this->solar->apparentSiderealTime,
            $this->solar->rightAscension,
            $this->prevSolar->rightAscension,
            $this->nextSolar->rightAscension,
            $this->solar->declination,
            $this->prevSolar->declination,
            $this->nextSolar->declination
        );

        $this->sunset = Astronomical::correctedHourAngle(
            $m0,
            $solarAltitude,
            $coordinates,
            true,
            $this->solar->apparentSiderealTime,
            $this->solar->rightAscension,
            $this->prevSolar->rightAscension,
            $this->nextSolar->rightAscension,
            $this->solar->declination,
            $this->prevSolar->declination,
            $this->nextSolar->declination
        );
    }

    /**
     * Calculate hour angle for afternoon prayer (Asr)
     */
    public function afternoon(float $shadowLength): float
    {
        $tangent = abs($this->observer->latitude - $this->solar->declination);
        $inverse = $shadowLength + tan(MathUtils::degreesToRadians($tangent));
        $angle = MathUtils::radiansToDegrees(atan(1.0 / $inverse));
        return $this->hourAngle($angle, true);
    }

    /**
     * Calculate hour angle
     */
    public function hourAngle(float $angle, bool $afterTransit): float
    {
        return Astronomical::correctedHourAngle(
            $this->approxTransit,
            $angle,
            $this->observer,
            $afterTransit,
            $this->solar->apparentSiderealTime,
            $this->solar->rightAscension,
            $this->prevSolar->rightAscension,
            $this->nextSolar->rightAscension,
            $this->solar->declination,
            $this->prevSolar->declination,
            $this->nextSolar->declination
        );
    }
}
