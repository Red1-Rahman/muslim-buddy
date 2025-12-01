<?php

namespace App\Services\Prayer;

/**
 * Calculation methods for prayer times
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class CalculationMethod
{
    /**
     * Muslim World League
     */
    public static function muslimWorldLeague(): CalculationParameters
    {
        $params = new CalculationParameters('MuslimWorldLeague', 18, 17);
        $params->methodAdjustments['dhuhr'] = 1;
        return $params;
    }

    /**
     * Egyptian General Authority of Survey
     */
    public static function egyptian(): CalculationParameters
    {
        $params = new CalculationParameters('Egyptian', 19.5, 17.5);
        $params->methodAdjustments['dhuhr'] = 1;
        return $params;
    }

    /**
     * University of Islamic Sciences, Karachi
     */
    public static function karachi(): CalculationParameters
    {
        $params = new CalculationParameters('Karachi', 18, 18);
        $params->methodAdjustments['dhuhr'] = 1;
        return $params;
    }

    /**
     * Umm al-Qura University, Makkah
     */
    public static function ummAlQura(): CalculationParameters
    {
        return new CalculationParameters('UmmAlQura', 18.5, 0, 90);
    }

    /**
     * Dubai
     */
    public static function dubai(): CalculationParameters
    {
        $params = new CalculationParameters('Dubai', 18.2, 18.2);
        $params->methodAdjustments['sunrise'] = -3;
        $params->methodAdjustments['dhuhr'] = 3;
        $params->methodAdjustments['asr'] = 3;
        $params->methodAdjustments['maghrib'] = 3;
        return $params;
    }

    /**
     * Islamic Society of North America
     */
    public static function northAmerica(): CalculationParameters
    {
        $params = new CalculationParameters('NorthAmerica', 15, 15);
        $params->methodAdjustments['dhuhr'] = 1;
        return $params;
    }

    /**
     * Kuwait
     */
    public static function kuwait(): CalculationParameters
    {
        return new CalculationParameters('Kuwait', 18, 17.5);
    }

    /**
     * Qatar
     */
    public static function qatar(): CalculationParameters
    {
        return new CalculationParameters('Qatar', 18, 0, 90);
    }

    /**
     * Singapore
     */
    public static function singapore(): CalculationParameters
    {
        $params = new CalculationParameters('Singapore', 20, 18);
        $params->methodAdjustments['dhuhr'] = 1;
        return $params;
    }

    /**
     * Tehran
     */
    public static function tehran(): CalculationParameters
    {
        $params = new CalculationParameters('Tehran', 17.7, 14, 0, 4.5);
        return $params;
    }

    /**
     * Turkey
     */
    public static function turkey(): CalculationParameters
    {
        $params = new CalculationParameters('Turkey', 18, 17);
        $params->methodAdjustments['sunrise'] = -7;
        $params->methodAdjustments['dhuhr'] = 5;
        $params->methodAdjustments['asr'] = 4;
        $params->methodAdjustments['maghrib'] = 7;
        return $params;
    }

    /**
     * Moonsighting Committee
     */
    public static function moonsightingCommittee(): CalculationParameters
    {
        $params = new CalculationParameters('MoonsightingCommittee', 18, 18);
        $params->methodAdjustments['dhuhr'] = 5;
        $params->methodAdjustments['maghrib'] = 3;
        return $params;
    }

    /**
     * Other method for custom calculations
     */
    public static function other(): CalculationParameters
    {
        return new CalculationParameters('Other', 0, 0);
    }
}
