<?php

namespace App\Helpers;

use App\Models\Section;
use App\Models\UserProgress;
use Illuminate\Support\Str;

class SectionDurationHelper
{
    public static function saveDuration(Section $section, $duration)
    {
        $duration = self::formatTime($duration);

        $userProgress = UserProgress::firstOrCreate([
            'user_id' =>  auth()->user()->id,
            'section_id' =>  $section->id,
            'module_id' => $section->module->id,
            'training_id' => $section->module->training->id,
        ]);

        $userProgress->update([
            'duration' => self::sumDurations($userProgress->duration,$duration),
            'completed_at' => now(),
        ]);
    }

    public static function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $formattedSeconds = str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);

        return "{$formattedHours}:{$formattedMinutes}:{$formattedSeconds}";
    }

    protected static function sumDurations($duration1,$duration2)
    {
        $duration1 = Str::contains($duration1,':') ?  $duration1 : '00:00:00';
        $duration2 = Str::contains($duration2,':') ?  $duration2 : '00:00:00';

        $seconds1 = explode(':', $duration1);
        $seconds2 = explode(':', $duration2);

        $sumHours = (int) $seconds1[0] + (int) $seconds2[0];
        $sumMinutes = (int) $seconds1[1] + (int) $seconds2[1];
        $sumSeconds = (int) $seconds1[2] + (int) $seconds2[2];

        // Perform carry-over if necessary
        if ($sumSeconds >= 60) {
            $sumSeconds -= 60;
            $sumMinutes++;
        }

        if ($sumMinutes >= 60) {
            $sumMinutes -= 60;
            $sumHours++;
        }

        // Limit the resulting time to 23:59:59
        if ($sumHours > 23) {
            $sumHours = 23;
            $sumMinutes = 59;
            $sumSeconds = 59;
        }

        $sumTime = sprintf('%02d:%02d:%02d', $sumHours, $sumMinutes, $sumSeconds);
        return $sumTime;
    }
}
