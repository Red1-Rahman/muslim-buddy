<?php

namespace App\Services\Astronomy;

use DateTime;

/**
 * Astronomical calculations for solar position and time
 * Based on "Astronomical Algorithms" by Jean Meeus
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class Astronomical
{
    /**
     * Calculate Julian Day from Gregorian date
     */
    public static function julianDay(int $year, int $month, int $day, float $hours = 0): float
    {
        // Adjust for month
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }

        $a = floor($year / 100);
        $b = 2 - $a + floor($a / 4);

        $jd = floor(365.25 * ($year + 4716)) + 
              floor(30.6001 * ($month + 1)) + 
              $day + $b - 1524.5;

        return $jd + ($hours / 24.0);
    }

    /**
     * Calculate Julian Century from Julian Day
     */
    public static function julianCentury(float $julianDay): float
    {
        return ($julianDay - 2451545.0) / 36525.0;
    }

    /**
     * The geometric mean longitude of the sun in degrees
     * Equation from Astronomical Algorithms page 163
     */
    public static function meanSolarLongitude(float $julianCentury): float
    {
        $T = $julianCentury;
        $term1 = 280.4664567;
        $term2 = 36000.76983 * $T;
        $term3 = 0.0003032 * pow($T, 2);
        $L0 = $term1 + $term2 + $term3;
        return MathUtils::unwindAngle($L0);
    }

    /**
     * The geometric mean longitude of the moon in degrees
     * Equation from Astronomical Algorithms page 144
     */
    public static function meanLunarLongitude(float $julianCentury): float
    {
        $T = $julianCentury;
        $term1 = 218.3165;
        $term2 = 481267.8813 * $T;
        $Lp = $term1 + $term2;
        return MathUtils::unwindAngle($Lp);
    }

    /**
     * Ascending lunar node longitude
     * Equation from Astronomical Algorithms page 144
     */
    public static function ascendingLunarNodeLongitude(float $julianCentury): float
    {
        $T = $julianCentury;
        $term1 = 125.04452;
        $term2 = 1934.136261 * $T;
        $term3 = 0.0020708 * pow($T, 2);
        $term4 = pow($T, 3) / 450000;
        $Omega = $term1 - $term2 + $term3 + $term4;
        return MathUtils::unwindAngle($Omega);
    }

    /**
     * The mean anomaly of the sun
     * Equation from Astronomical Algorithms page 163
     */
    public static function meanSolarAnomaly(float $julianCentury): float
    {
        $T = $julianCentury;
        $term1 = 357.52911;
        $term2 = 35999.05029 * $T;
        $term3 = 0.0001537 * pow($T, 2);
        $M = $term1 + $term2 - $term3;
        return MathUtils::unwindAngle($M);
    }

    /**
     * The Sun's equation of the center in degrees
     * Equation from Astronomical Algorithms page 164
     */
    public static function solarEquationOfTheCenter(float $julianCentury, float $meanAnomaly): float
    {
        $T = $julianCentury;
        $Mrad = MathUtils::degreesToRadians($meanAnomaly);
        $term1 = (1.914602 - 0.004817 * $T - 0.000014 * pow($T, 2)) * sin($Mrad);
        $term2 = (0.019993 - 0.000101 * $T) * sin(2 * $Mrad);
        $term3 = 0.000289 * sin(3 * $Mrad);
        return $term1 + $term2 + $term3;
    }

    /**
     * The apparent longitude of the Sun
     * Equation from Astronomical Algorithms page 164
     */
    public static function apparentSolarLongitude(float $julianCentury, float $meanLongitude): float
    {
        $T = $julianCentury;
        $L0 = $meanLongitude;
        $longitude = $L0 + self::solarEquationOfTheCenter($T, self::meanSolarAnomaly($T));
        $Omega = 125.04 - 1934.136 * $T;
        $Lambda = $longitude - 0.00569 - 0.00478 * sin(MathUtils::degreesToRadians($Omega));
        return MathUtils::unwindAngle($Lambda);
    }

    /**
     * The mean obliquity of the ecliptic
     * Equation from Astronomical Algorithms page 147
     */
    public static function meanObliquityOfTheEcliptic(float $julianCentury): float
    {
        $T = $julianCentury;
        $term1 = 23.439291;
        $term2 = 0.013004167 * $T;
        $term3 = 0.0000001639 * pow($T, 2);
        $term4 = 0.0000005036 * pow($T, 3);
        return $term1 - $term2 - $term3 + $term4;
    }

    /**
     * The apparent obliquity of the ecliptic
     * Equation from Astronomical Algorithms page 165
     */
    public static function apparentObliquityOfTheEcliptic(float $julianCentury, float $meanObliquityOfTheEcliptic): float
    {
        $T = $julianCentury;
        $Epsilon0 = $meanObliquityOfTheEcliptic;
        $O = 125.04 - 1934.136 * $T;
        return $Epsilon0 + 0.00256 * cos(MathUtils::degreesToRadians($O));
    }

    /**
     * Mean sidereal time
     * Equation from Astronomical Algorithms page 165
     */
    public static function meanSiderealTime(float $julianCentury): float
    {
        $T = $julianCentury;
        $JD = $T * 36525 + 2451545.0;
        $term1 = 280.46061837;
        $term2 = 360.98564736629 * ($JD - 2451545);
        $term3 = 0.000387933 * pow($T, 2);
        $term4 = pow($T, 3) / 38710000;
        $Theta = $term1 + $term2 + $term3 - $term4;
        return MathUtils::unwindAngle($Theta);
    }

    /**
     * Nutation in longitude
     * Equation from Astronomical Algorithms page 144
     */
    public static function nutationInLongitude(float $julianCentury, float $solarLongitude, float $lunarLongitude, float $ascendingNode): float
    {
        $L0 = $solarLongitude;
        $Lp = $lunarLongitude;
        $Omega = $ascendingNode;
        $term1 = (-17.2 / 3600) * sin(MathUtils::degreesToRadians($Omega));
        $term2 = (1.32 / 3600) * sin(2 * MathUtils::degreesToRadians($L0));
        $term3 = (0.23 / 3600) * sin(2 * MathUtils::degreesToRadians($Lp));
        $term4 = (0.21 / 3600) * sin(2 * MathUtils::degreesToRadians($Omega));
        return $term1 - $term2 - $term3 + $term4;
    }

    /**
     * Nutation in obliquity
     * Equation from Astronomical Algorithms page 144
     */
    public static function nutationInObliquity(float $julianCentury, float $solarLongitude, float $lunarLongitude, float $ascendingNode): float
    {
        $L0 = $solarLongitude;
        $Lp = $lunarLongitude;
        $Omega = $ascendingNode;
        $term1 = (9.2 / 3600) * cos(MathUtils::degreesToRadians($Omega));
        $term2 = (0.57 / 3600) * cos(2 * MathUtils::degreesToRadians($L0));
        $term3 = (0.1 / 3600) * cos(2 * MathUtils::degreesToRadians($Lp));
        $term4 = (0.09 / 3600) * cos(2 * MathUtils::degreesToRadians($Omega));
        return $term1 + $term2 + $term3 - $term4;
    }

    /**
     * Altitude of celestial body
     * Equation from Astronomical Algorithms page 93
     */
    public static function altitudeOfCelestialBody(float $observerLatitude, float $declination, float $localHourAngle): float
    {
        $Phi = $observerLatitude;
        $delta = $declination;
        $H = $localHourAngle;
        $term1 = sin(MathUtils::degreesToRadians($Phi)) * sin(MathUtils::degreesToRadians($delta));
        $term2 = cos(MathUtils::degreesToRadians($Phi)) * cos(MathUtils::degreesToRadians($delta)) * cos(MathUtils::degreesToRadians($H));
        return MathUtils::radiansToDegrees(asin($term1 + $term2));
    }

    /**
     * Approximate transit
     * Equation from Astronomical Algorithms page 102
     */
    public static function approximateTransit(float $longitude, float $siderealTime, float $rightAscension): float
    {
        $L = $longitude;
        $Theta0 = $siderealTime;
        $a2 = $rightAscension;
        $Lw = $L * -1;
        return MathUtils::normalizeToScale(($a2 + $Lw - $Theta0) / 360, 1);
    }

    /**
     * Corrected transit (solar noon)
     */
    public static function correctedTransit(float $approximateTransit, float $longitude, float $siderealTime, float $rightAscension, float $previousRightAscension, float $nextRightAscension): float
    {
        $m0 = $approximateTransit;
        $L = $longitude;
        $Theta0 = $siderealTime;
        $a2 = $rightAscension;
        $a1 = $previousRightAscension;
        $a3 = $nextRightAscension;
        $Lw = $L * -1;
        $Theta = MathUtils::unwindAngle($Theta0 + 360.985647 * $m0);
        $a = MathUtils::unwindAngle(self::interpolateAngles($a2, $a1, $a3, $m0));
        $H = MathUtils::quadrantShiftAngle($Theta - $Lw - $a);
        $dm = $H / -360;
        return ($m0 + $dm) * 24;
    }

    /**
     * Corrected hour angle
     */
    public static function correctedHourAngle(
        float $approximateTransit,
        float $angle,
        Coordinates $coordinates,
        bool $afterTransit,
        float $siderealTime,
        float $rightAscension,
        float $previousRightAscension,
        float $nextRightAscension,
        float $declination,
        float $previousDeclination,
        float $nextDeclination
    ): float {
        $m0 = $approximateTransit;
        $h0 = $angle;
        $Theta0 = $siderealTime;
        $a2 = $rightAscension;
        $a1 = $previousRightAscension;
        $a3 = $nextRightAscension;
        $d2 = $declination;
        $d1 = $previousDeclination;
        $d3 = $nextDeclination;

        $Lw = $coordinates->longitude * -1;
        $term1 = sin(MathUtils::degreesToRadians($h0)) - 
                 sin(MathUtils::degreesToRadians($coordinates->latitude)) * 
                 sin(MathUtils::degreesToRadians($d2));
        $term2 = cos(MathUtils::degreesToRadians($coordinates->latitude)) * 
                 cos(MathUtils::degreesToRadians($d2));
        $H0 = MathUtils::radiansToDegrees(acos($term1 / $term2));
        $m = $afterTransit ? $m0 + ($H0 / 360) : $m0 - ($H0 / 360);

        $Theta = MathUtils::unwindAngle($Theta0 + 360.985647 * $m);
        $a = MathUtils::unwindAngle(self::interpolateAngles($a2, $a1, $a3, $m));
        $delta = self::interpolate($d2, $d1, $d3, $m);
        $H = $Theta - $Lw - $a;
        $h = self::altitudeOfCelestialBody($coordinates->latitude, $delta, $H);
        $dm = ($h - $h0) / 
              (360 * cos(MathUtils::degreesToRadians($delta)) * 
               cos(MathUtils::degreesToRadians($coordinates->latitude)) * 
               sin(MathUtils::degreesToRadians($H)));

        return ($m + $dm) * 24;
    }

    /**
     * Interpolate between three values
     */
    private static function interpolate(float $y2, float $y1, float $y3, float $n): float
    {
        $a = $y2 - $y1;
        $b = $y3 - $y2;
        $c = $b - $a;
        return $y2 + (($n / 2) * ($a + $b + ($n * $c)));
    }

    /**
     * Interpolate angles
     */
    private static function interpolateAngles(float $y2, float $y1, float $y3, float $n): float
    {
        $a = MathUtils::unwindAngle($y2 - $y1);
        $b = MathUtils::unwindAngle($y3 - $y2);
        $c = $b - $a;
        return $y2 + (($n / 2) * ($a + $b + ($n * $c)));
    }
}
