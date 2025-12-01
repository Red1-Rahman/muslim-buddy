<?php

namespace App\Services\Astronomy;

/**
 * Solar coordinates calculations (declination, right ascension, sidereal time)
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class SolarCoordinates
{
    public float $declination;
    public float $rightAscension;
    public float $apparentSiderealTime;

    public function __construct(float $julianDay)
    {
        $T = Astronomical::julianCentury($julianDay);
        $L0 = Astronomical::meanSolarLongitude($T);
        $Lp = Astronomical::meanLunarLongitude($T);
        $Omega = Astronomical::ascendingLunarNodeLongitude($T);
        $Lambda = MathUtils::degreesToRadians(Astronomical::apparentSolarLongitude($T, $L0));
        $Theta0 = Astronomical::meanSiderealTime($T);
        $dPsi = Astronomical::nutationInLongitude($T, $L0, $Lp, $Omega);
        $dEpsilon = Astronomical::nutationInObliquity($T, $L0, $Lp, $Omega);
        $Epsilon0 = Astronomical::meanObliquityOfTheEcliptic($T);
        $EpsilonApparent = MathUtils::degreesToRadians(
            Astronomical::apparentObliquityOfTheEcliptic($T, $Epsilon0)
        );

        // Declination: The declination of the sun
        // Equation from Astronomical Algorithms page 165
        $this->declination = MathUtils::radiansToDegrees(
            asin(sin($EpsilonApparent) * sin($Lambda))
        );

        // Right Ascension
        // Equation from Astronomical Algorithms page 165
        $this->rightAscension = MathUtils::unwindAngle(
            MathUtils::radiansToDegrees(
                atan2(
                    cos($EpsilonApparent) * sin($Lambda),
                    cos($Lambda)
                )
            )
        );

        // Apparent sidereal time
        // Equation from Astronomical Algorithms page 88
        $this->apparentSiderealTime = $Theta0 + 
            ($dPsi * 3600 * cos(MathUtils::degreesToRadians($Epsilon0 + $dEpsilon))) / 3600;
    }
}
