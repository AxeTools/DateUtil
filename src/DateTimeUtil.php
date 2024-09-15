<?php

namespace AxeTools\Utilities\DateTime;

use AxeTools\Traits;
use DateTime;
use InvalidArgumentException;

class DateTimeUtil {
    use Traits\BitFlag\BitFlagTrait;

    /**
     * Do not adjust holidays days for observability.
     */
    public const HOLIDAY_OBSERVED_NONE = 0b001;

    /**
     * Holidays that occur on a Saturday should be moved to a Friday for Observance.
     */
    public const HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY = 0b010;

    /**
     * Holidays that occur on a Sunday should be moved to a Monday for Observance.
     */
    public const HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY = 0b100;

    /**
     * Inclusively consider the exact start and exact end date when checking the reference date time.
     */
    public const IS_BETWEEN_INCLUSIVE = 0b11;
    /**
     * Exclusively consider the exact start and end date time when checking the reference date time.
     */
    public const IS_BETWEEN_EXCLUSIVE = 0b00;

    /**
     * Only consider inclusively the start date time when checking the reference date time, the end date time will be
     * excluded.
     */
    public const IS_BETWEEN_INCLUSIVE_START = 0b01;

    /**
     * Only consider inclusively the end date time when checking the reference date time, the exact start time will be
     * excluded.
     */
    public const IS_BETWEEN_INCLUSIVE_END = 0b10;

    /**
     * The last day of the month is a special relative date, this needs to be used in conjunction with the
     * Week::ABSOLUTE.
     */
    public const RELATIVE_DAY_LAST = -1;

    protected const DATETIME_FORMAT = 'Y-m-d H:i';

    /**
     * Determine if today is a Holiday from the array of provided **Holiday** objects accounting for the observability
     * rules of the Holiday objects themselves as well as the provided observation options.
     *
     * @param array<Holiday> $holidays            an array of Holiday instances appropriate for your usage
     * @param DateTime       $referenceDatetime   This is the reference date to compare, used in unit tests, null for current
     *                                            date
     * @param int            $observation_options Bitwise options for how to modify the relative holidays for
     *                                            observance
     *
     * @return bool return if the reference date is in the provided holiday array in accordance with the observation options provided
     */
    public static function isHoliday(array $holidays, \DateTimeInterface $referenceDatetime, $observation_options = self::HOLIDAY_OBSERVED_NONE): bool {
        foreach ($holidays as $holiday) {
            if (self::hasFlag($observation_options, self::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY)) {
                if ($holiday->isObservedShift() && DayOfWeek::SATURDAY == $holiday->getDatetime()->format('N')) {
                    $holiday->getDatetime()->modify('-1 day');
                }
            }

            if (self::hasFlag($observation_options, self::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY)) {
                if ($holiday->isObservedShift() && DayOfWeek::SUNDAY == $holiday->getDatetime()->format('N')) {
                    $holiday->getDatetime()->modify('+1 day');
                }
            }

            if ($referenceDatetime->format('Ymd') == $holiday->getDatetime()->format('Ymd')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a DateTime object from relative date parts.  There are some helper objects that are collections of
     * constants to help document the relative behavior that is being asked for.  The DateTime object  that is returned
     * will have it's time set to 00:00:00.  The Timezone will be set to your php instances default timezone.
     *
     * **Examples:**
     *
     * <pre>
     *     Relative first Monday in September this year createRelativeDateTime(9, DayOfWeek::MONDAY, Week::FIRST)
     *     Relative the 4th Thursday of November createRelativeDateTime(11, DayOfWeek::THURSDAY, Week::FOURTH)
     *     Relative the last tuesday of the current month createRelativeDateTime(null, DayOfWeek::TUESDAY, Week::LAST)
     *     Relative the last day of the current month createRelativeDateTime(null, self::RELATIVE_DAY_LAST)
     * </pre>
     *
     *   If you need the last day of a month see `getAbsoluteDateTime()`
     *
     * @param int|null $month       The month of the year 1 = january, 12 = december, if **null** the current month is to be
     *                              used
     * @param int|null $dayOfWeek   The ISO8601 day of the week 1 = Monday, 7 = Sunday. <br>***For the last day of a
     *                              month use getAbsoluteDateTime()***
     * @param int|null $weekOfMonth This is the week of the month 1-5 and -1 to mean the last occurrence in
     *                              the month.  When in doubt the **last** occurrence is safer to use than the 5th, which may lead to the
     *                              next month (see tests).  <br>***Be VERY careful when using relative dates as bounding conditions they may
     *                              not consistently occur in the order you expect***
     * @param int|null $year        Optional, The year, if **null** the current year is used
     *
     * @return DateTime a DateTime object for the provided relative date with the time set to 00:00:00
     *
     * @throws InvalidArgumentException if dayOfWeek or weekOfMonth are not set
     *
     * @see DateTimeUtilTest::createRelativeDateTime()
     * @see DayOfWeek
     * @see Week
     */
    public static function createRelativeDateTime(?int $month, ?int $dayOfWeek, ?int $weekOfMonth, ?int $year = null): DateTime {
        $year = (null === $year) ? date('Y') : $year;
        $month = (null === $month) ? date('m') : $month;

        if (null === $dayOfWeek || $dayOfWeek < DayOfWeek::MONDAY || $dayOfWeek > DayOfWeek::SUNDAY) {
            throw new InvalidArgumentException('dayOfWeek must be set and be between '.DayOfWeek::MONDAY.' and '.DayOfWeek::SUNDAY);
        }
        if (null === $weekOfMonth || $weekOfMonth < Week::LAST || $weekOfMonth > Week::FIFTH) {
            throw new InvalidArgumentException('weekOfMonth must be set and be between '.Week::LAST.' and '.Week::FIFTH);
        }

        $date = DateTime::createFromFormat(self::DATETIME_FORMAT, $year.'-'.$month.'-01 00:00');

        if (false === $date) {
            throw new InvalidArgumentException('Invalid date format');
        }

        $occurrence = Week::mapToName($weekOfMonth);
        $dayOfWeekName = DayOfWeek::mapToName($dayOfWeek);
        $sMonth = $date->format('F');
        $date->modify(strtolower(sprintf('%s %s of %s %d', $occurrence, $dayOfWeekName, $sMonth, $year)));

        return $date;
    }

    /**
     * Create a DateTime object for a specific date, or recurring date. The DateTime object that is returned
     * will have it's time set to 00:00:00.  The Timezone will be set to your php instances default timezone.
     *
     * **Examples:**
     *
     * <pre>
     *     Absolute date of April 13th, 2023 createAbsoluteDateTime(4, 13, 2023)
     *     Absolute 13th day of the current month in 2023 createAbsoluteDateTime(null, 13, 2023)
     *     Absolute April 13th of the current year createAbsoluteDateTime(4, 13)
     *     Absolute 13th of the current month createAbsoluteDateTime(null, 13)
     * </pre>
     *
     * @param int|null $month The month of the year 1 = january, 12 = december, if **null** the current month is to be
     *                        used
     * @param int|null $day   The day of the month, if set to DateTimeUtil::RELATIVE_LAST_DAY you will get the last day of the month requested
     * @param int|null $year  Optional, The year, if **null** the current year is used
     *
     * @return DateTime a DateTime object for the provided absolute date with the time set to 00:00:00
     */
    public static function createAbsoluteDateTime(?int $month = null, ?int $day = null, ?int $year = null): DateTime {
        $year = (null === $year) ? date('Y') : $year;
        $month = (null === $month) ? date('m') : $month;
        $day = (null === $day) ? date('d') : $day;

        if (self::RELATIVE_DAY_LAST === $day) { // last day of the month is next month's 0 day
            ++$month;
            $day = 0;
        }
        $date = DateTime::createFromFormat(self::DATETIME_FORMAT, $year.'-'.$month.'-'.$day.' 00:00');
        if (false === $date) {
            throw new InvalidArgumentException('Invalid date format');
        }

        return $date;
    }

    /**
     * Return an array of Holiday Objects for the Annual National United States Holidays for the current year.
     *
     * @param int|null $year A reference year if generating the array for a year other than the current year
     *
     * @return array<Holiday>
     */
    public static function usFederalHolidays(?int $year = null): array {
        return [
            Holiday::create(
                self::createAbsoluteDateTime(1, 1, $year),
                'New Year\'s Day',
                'New Year\'s Day',
                'New Year\'s Day is the first day of the Gregorian calendar.',
                true
            ),
            Holiday::create(
                self::createRelativeDateTime(1, DayOfWeek::MONDAY, Week::THIRD, $year),
                'Martin Luther King, Jr. Day',
                'MLK, Jr. Day',
                'Martin Luther King Day marks the anniversary of the date of birth of the influential American civil right leader of the same name.'
            ),
            Holiday::create(
                self::createRelativeDateTime(2, DayOfWeek::MONDAY, Week::THIRD, $year),
                'Presidents\' Day',
                'President\'s Day',
                'Washington\'s Birthday, or Presidents\' Day, honors the life and work of the first president of the United States, George Washington.'
            ),
            Holiday::create(
                self::createRelativeDateTime(5, DayOfWeek::MONDAY, Week::FIRST, $year),
                'Memorial Day',
                'Memorial Day',
                'Memorial Day commemorates all Americans who have died in military service for the United States.'
            ),
            Holiday::create(
                self::createAbsoluteDateTime(6, 19, $year),
                'Juneteenth National Freedom Day',
                'Juneteenth',
                'Juneteenth National Freedom Day is a state observance in the USA.',
                true
            ),
            Holiday::create(
                self::createAbsoluteDateTime(7, 4, $year),
                'Independence Day',
                'Independence Day',
                'On Independence Day, Americans celebrate the anniversary of publication of the Declaration of Independence from Great Britain in 1776.',
                true
            ),
            Holiday::create(
                self::createRelativeDateTime(9, DayOfWeek::MONDAY, Week::FIRST, $year),
                'Labor Day',
                'Labor Day',
                'Labor Day is a federal holiday in the United States. It gives workers a day of rest and it celebrates their contribution to the American economy.'
            ),
            Holiday::create(
                self::createRelativeDateTime(10, DayOfWeek::MONDAY, Week::SECOND, $year),
                'Columbus Day',
                'Columbus Day',
                'Columbus Day celebrates 15th century explorer Christopher Columbus\'s arrival in America in 1492.'
            ),
            Holiday::create(
                self::createAbsoluteDateTime(11, 11, $year),
                'Veterans Day',
                'Veterans Day',
                'Veterans Day in the USA is a holiday to honor all who have served in the United States Military Services.',
                true
            ),
            Holiday::create(
                self::createRelativeDateTime(11, DayOfWeek::THURSDAY, Week::FOURTH, $year),
                'Thanksgiving Day',
                'Thanksgiving Day',
                'Thanksgiving Day in the United States is traditionally a holiday to give thanks for the food collected at the end of the harvest season.'
            ),
            Holiday::create(
                self::createAbsoluteDateTime(12, 25, $year),
                'Christmas Day',
                'Christmas Day',
                'Christmas Day celebrates Jesus Christ\'s birth.',
                true
            ),
        ];
    }

    /**
     * Is the check time, between the start and end times.  By default, the check is done inclusively but can be changed
     * to be an exclusive check.
     *
     * @param \DateTimeInterface $start             The starting time for the between check
     * @param \DateTimeInterface $end               The ending time for the between check
     * @param \DateTimeInterface $referenceDatetime The reference date the check to see if it is in between the give start and end times
     * @param int                $inclusiveOptions  optional, should the exact end and start times be included in the check (default) or excluded use the **DateTimeUtil::ISBETWEEN_** constants to define behavior
     */
    public static function isBetween(\DateTimeInterface $start, \DateTimeInterface $end, \DateTimeInterface $referenceDatetime, int $inclusiveOptions = DateTimeUtil::IS_BETWEEN_INCLUSIVE): bool {
        $before_end = (self::hasFlag($inclusiveOptions, self::IS_BETWEEN_INCLUSIVE_END)) ? ($end >= $referenceDatetime) : ($end > $referenceDatetime);
        $after_start = (self::hasFlag($inclusiveOptions, self::IS_BETWEEN_INCLUSIVE_START)) ? ($start <= $referenceDatetime) : ($start < $referenceDatetime);

        return $after_start && $before_end;
    }
}
