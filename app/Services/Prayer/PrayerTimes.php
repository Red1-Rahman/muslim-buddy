<?php

namespace App\Services\Prayer;

use App\Services\Astronomy\Astronomical;
use App\Services\Astronomy\Coordinates;
use App\Services\Astronomy\SolarTime;
use DateTime;
use DateInterval;

/**
 * Prayer Times Calculator
 * Calculates the five daily Islamic prayer times and forbidden times
 * 
 * @author Redwan Rahman (github.com/Red1-Rahman)
 */
class PrayerTimes
{
    public DateTime $fajr;
    public DateTime $sunrise;
    public DateTime $dhuhr;
    public DateTime $asr;
    public DateTime $sunset;
    public DateTime $maghrib;
    public DateTime $isha;
    
    // Forbidden prayer times
    public ?DateTime $forbiddenAfterFajr = null;
    public ?DateTime $forbiddenBeforeDhuhr = null;
    public ?DateTime $forbiddenAfterAsr = null;

    public Coordinates $coordinates;
    public DateTime $date;
    public CalculationParameters $calculationParameters;

    public function __construct(
        Coordinates $coordinates,
        DateTime $date,
        CalculationParameters $calculationParameters
    ) {
        $this->coordinates = $coordinates;
        $this->date = $date;
        $this->calculationParameters = $calculationParameters;

        $this->calculate();
    }

    private function calculate(): void
    {
        $solarTime = new SolarTime($this->date, $this->coordinates);
        
        $year = (int)$this->date->format('Y');
        $month = (int)$this->date->format('m');
        $day = (int)$this->date->format('d');

        // Calculate Dhuhr (solar noon) - no timezone offset needed as we handle it in DateTime creation
        $this->dhuhr = $this->timeComponentsToDateTime($solarTime->transit, $year, $month, $day);
        $this->applyMethodAdjustment($this->dhuhr, 'dhuhr');

        // Calculate Sunrise
        $this->sunrise = $this->timeComponentsToDateTime($solarTime->sunrise, $year, $month, $day);
        $this->applyMethodAdjustment($this->sunrise, 'sunrise');

        // Calculate Sunset  
        $this->sunset = $this->timeComponentsToDateTime($solarTime->sunset, $year, $month, $day);
        $this->applyMethodAdjustment($this->sunset, 'sunset');

        // Calculate Asr using proper shadow calculation
        $asrTime = $solarTime->afternoon($this->calculationParameters->shadowLength());
        $this->asr = $this->timeComponentsToDateTime($asrTime, $year, $month, $day);
        $this->applyMethodAdjustment($this->asr, 'asr');

        // Calculate Fajr using depression angle - this should be in early morning
        $fajrTime = $solarTime->hourAngle(-1 * $this->calculationParameters->fajrAngle, false);
        $this->fajr = $this->timeComponentsToDateTime($fajrTime, $year, $month, $day);
        
        // If Fajr time is after noon (incorrect calculation), move to previous day
        if ($this->fajr->format('H') > 12) {
            $this->fajr->sub(new DateInterval('P1D'));
        }
        $this->applyMethodAdjustment($this->fajr, 'fajr');

        // Calculate Maghrib (usually same as sunset with small adjustment)
        $this->maghrib = clone $this->sunset;
        $this->applyMethodAdjustment($this->maghrib, 'maghrib');

        // Calculate Isha
        if ($this->calculationParameters->ishaInterval > 0) {
            // Fixed interval method (e.g., Umm Al-Qura)
            $this->isha = clone $this->maghrib;
            $this->isha->add(new DateInterval('PT' . (int)($this->calculationParameters->ishaInterval * 60) . 'M'));
        } else {
            // Depression angle method
            $ishaTime = $solarTime->hourAngle(-1 * $this->calculationParameters->ishaAngle, true);
            $this->isha = $this->timeComponentsToDateTime($ishaTime, $year, $month, $day);
        }
        $this->applyMethodAdjustment($this->isha, 'isha');

        // Calculate forbidden prayer times
        $this->calculateForbiddenTimes();
    }
    
    /**
     * Apply method-specific time adjustments
     */
    private function applyMethodAdjustment(DateTime $time, string $prayer): void
    {
        $adjustment = $this->calculationParameters->methodAdjustments[$prayer] ?? 0;
        if ($adjustment != 0) {
            $interval = $adjustment > 0 ? 'PT' . $adjustment . 'M' : 'PT' . abs($adjustment) . 'M';
            if ($adjustment > 0) {
                $time->add(new DateInterval($interval));
            } else {
                $time->sub(new DateInterval($interval));
            }
        }
    }

    /**
     * Calculate forbidden prayer times (makruh times)
     * 1. After Fajr until 15-20 minutes after sunrise
     * 2. When sun is at zenith (before Dhuhr)
     * 3. After Asr until sunset
     */
    private function calculateForbiddenTimes(): void
    {
        // Forbidden time after Fajr (until ~20 minutes after sunrise)
        $this->forbiddenAfterFajr = clone $this->sunrise;
        $this->forbiddenAfterFajr->add(new DateInterval('PT20M'));

        // Forbidden time before Dhuhr (10 minutes before and after solar noon)
        $this->forbiddenBeforeDhuhr = clone $this->dhuhr;
        $this->forbiddenBeforeDhuhr->sub(new DateInterval('PT10M'));

        // Forbidden time after Asr (until sunset)
        $this->forbiddenAfterAsr = clone $this->asr;
    }

    /**
     * Convert time components (hours) to DateTime with proper timezone handling
     */
    private function timeComponentsToDateTime(float $hours, int $year, int $month, int $day): DateTime
    {
        // The astronomical calculations return time in universal time
        // We need to convert this to local solar time for the longitude
        
        // Calculate the longitude time offset
        $longitudeOffset = $this->coordinates->longitude / 15.0; // 15 degrees per hour
        
        // Apply longitude offset to get local solar time
        $localHours = $hours + $longitudeOffset;
        
        // Handle day boundaries
        $dayOffset = 0;
        if ($localHours < 0) {
            $dayOffset = -1;
            $localHours += 24;
        } elseif ($localHours >= 24) {
            $dayOffset = floor($localHours / 24);
            $localHours = fmod($localHours, 24);
        }
        
        // Extract hour and minute components
        $totalMinutes = $localHours * 60;
        $hour = floor($totalMinutes / 60);
        $minute = round($totalMinutes - ($hour * 60));
        
        // Ensure valid hour and minute ranges
        $hour = max(0, min(23, (int)$hour));
        $minute = max(0, min(59, (int)$minute));
        
        // Create DateTime with the same timezone as the input date
        $dateTime = new DateTime('now', $this->date->getTimezone());
        $dateTime->setDate($year, $month, $day);
        $dateTime->setTime($hour, $minute, 0);
        
        // Apply day offset if needed
        if ($dayOffset != 0) {
            $interval = $dayOffset > 0 ? 'P' . $dayOffset . 'D' : 'P' . abs($dayOffset) . 'D';
            if ($dayOffset > 0) {
                $dateTime->add(new DateInterval($interval));
            } else {
                $dateTime->sub(new DateInterval($interval));
            }
        }
        
        return $dateTime;
    }

    /**
     * Get current prayer based on time
     */
    public function currentPrayer(DateTime $time = null): ?string
    {
        $time = $time ?? new DateTime();
        
        if ($time >= $this->isha || $time < $this->fajr) {
            return 'isha';
        } elseif ($time >= $this->maghrib) {
            return 'maghrib';
        } elseif ($time >= $this->asr) {
            return 'asr';
        } elseif ($time >= $this->dhuhr) {
            return 'dhuhr';
        } elseif ($time >= $this->sunrise) {
            return 'sunrise';
        } elseif ($time >= $this->fajr) {
            return 'fajr';
        }
        
        return null;
    }

    /**
     * Get next prayer based on time
     */
    public function nextPrayer(DateTime $time = null): ?string
    {
        $time = $time ?? new DateTime();
        
        if ($time < $this->fajr) {
            return 'fajr';
        } elseif ($time < $this->dhuhr) {
            return 'dhuhr';
        } elseif ($time < $this->asr) {
            return 'asr';
        } elseif ($time < $this->maghrib) {
            return 'maghrib';
        } elseif ($time < $this->isha) {
            return 'isha';
        }
        
        return 'fajr'; // Next day's Fajr
    }

    /**
     * Check if current time is in forbidden prayer time
     */
    public function isForbiddenTime(DateTime $time = null): bool
    {
        $time = $time ?? new DateTime();
        
        // After Fajr until sunrise + 20 minutes
        if ($time >= $this->fajr && $time <= $this->forbiddenAfterFajr) {
            return true;
        }
        
        // Around solar noon (10 minutes before Dhuhr)
        if ($time >= $this->forbiddenBeforeDhuhr && $time <= $this->dhuhr) {
            return true;
        }
        
        // After Asr until sunset
        if ($time >= $this->forbiddenAfterAsr && $time <= $this->sunset) {
            return true;
        }
        
        return false;
    }

    /**
     * Get all prayer times as array
     */
    public function toArray(): array
    {
        return [
            'fajr' => $this->fajr->format('H:i'),
            'sunrise' => $this->sunrise->format('H:i'),
            'dhuhr' => $this->dhuhr->format('H:i'),
            'asr' => $this->asr->format('H:i'),
            'sunset' => $this->sunset->format('H:i'),
            'maghrib' => $this->maghrib->format('H:i'),
            'isha' => $this->isha->format('H:i'),
            'forbidden_times' => [
                'after_fajr' => $this->fajr->format('H:i') . ' - ' . $this->forbiddenAfterFajr->format('H:i'),
                'before_dhuhr' => $this->forbiddenBeforeDhuhr->format('H:i') . ' - ' . $this->dhuhr->format('H:i'),
                'after_asr' => $this->forbiddenAfterAsr->format('H:i') . ' - ' . $this->sunset->format('H:i')
            ]
        ];
    }
}
