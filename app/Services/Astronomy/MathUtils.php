<?php

namespace App\Services\Astronomy;

/**
 * Mathematical utility functions for astronomical calculations
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class MathUtils
{
    /**
     * Convert degrees to radians
     */
    public static function degreesToRadians(float $degrees): float
    {
        return $degrees * M_PI / 180.0;
    }

    /**
     * Convert radians to degrees
     */
    public static function radiansToDegrees(float $radians): float
    {
        return $radians * 180.0 / M_PI;
    }

    /**
     * Unwind an angle to be between 0 and 360
     */
    public static function unwindAngle(float $angle): float
    {
        return $angle - (360.0 * floor($angle / 360.0));
    }

    /**
     * Normalize a value to a specific scale
     */
    public static function normalizeToScale(float $value, float $scale): float
    {
        return $value - ($scale * floor($value / $scale));
    }

    /**
     * Calculate the quadrant shift angle
     */
    public static function quadrantShiftAngle(float $angle): float
    {
        if ($angle >= -180 && $angle <= 180) {
            return $angle;
        }
        return $angle - (360 * round($angle / 360));
    }
}
