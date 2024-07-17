<?php

namespace AxeTools\Utilities\DateTime;

/**
 * ISO8601 format Days of the Week 1 - Monday through 7 - Sunday
 */
class DayOfWeek {

    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @param int $dayOfWeek ISO8601 Day of the week number 1 - Monday to 7 - Sunday
     *
     * @return string lowercase full english name of the day of the week
     */
    public static function mapToName($dayOfWeek) {
        $mapDayOfWeek = [
            DayOfWeek::SUNDAY => 'sunday',
            DayOfWeek::MONDAY => 'monday',
            DayOfWeek::TUESDAY => 'tuesday',
            DayOfWeek::WEDNESDAY => 'wednesday',
            DayOfWeek::THURSDAY => 'thursday',
            DayOfWeek::FRIDAY => 'friday',
            DayOfWeek::SATURDAY => 'saturday'
        ];
        return $mapDayOfWeek[$dayOfWeek];
    }
}
