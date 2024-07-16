<?php

namespace AxeTools\Utilities\DateTime;

use DateTime;
use InvalidArgumentException;

class DateTimeUtil {

    /**
     * Do not adjust holidays days for observability
     */
    const HOLIDAY_OBSERVED_NONE = 0b001;

    /**
     * Holidays that occur on a Saturday should be moved to a Friday for Observance
     */
    const HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY = 0b010;

    /**
     * Holidays that occur on a Sunday should be moved to a Monday for Observance
     */
    const HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY = 0b100;

    /**
     * The last day of the month is a special relative date, this needs to be used in conjunction with the Week::ABSOLUTE
     */
    const RELATIVE_DAY_LAST = -1;

    const DATETIME_FORMAT = 'Y-m-d H:i';

    /**
     * Determine if today is a Holiday
     *
     * @param array<Holiday> $holidays an array of Holiday instances appropriate for your usage
     * @param int            $options Bitwise options for how to modify the relative holidays for observance
     * @param DateTime|null  $comparison This is the reference date to compare, used in unit tests, null for current date
     *
     * @return bool return if the current date is a Federal Holiday
     */
    public static function isHoliday($holidays = [], $options = self::HOLIDAY_OBSERVED_NONE, $comparison = null) {
        if (null === $comparison) $comparison = new DateTime();

        foreach ($holidays as $holiday) {
            if ($comparison->format('Ymd') == $holiday->getDatetime()->format('Ymd')) return true;
        }
        return false;
    }

    /**
     * Create a DateTime object from either relative or absolute date parts.  There are some helper objects that are
     * collections of constants to help document the relative behavior that is being asked for.
     *
     * **Examples:**
     *
     * <pre>
     *     Relative first Monday in September this year getRelativeDateTime(9, DayOfWeek::MONDAY, Week::FIRST)
     *     Relative the 4th Thursday of November getRelativeDateTime(11, DayOfWeek::THURSDAY, Week::FOURTH)
     *     Relative the last tuesday of the current month getRelativeDateTime(null, DayOfWeek::TUESDAY, Week::LAST)
     *     Relative the last day of the current month getRelativeDateTime(null, self::RELATIVE_DAY_LAST)
     * </pre>
     *
     * @param int|null $month The month of the year 1 = january, 12 = december, if **null** the current month is to be used
     * @param int $dayOfWeek The day of the week 0 = Sunday, 6 = Saturday. <br>***For the last day of a month use getAbsoluteDateTime()***
     * @param int $weekOfMonth This is the week of the month 1-5 and -1 to mean the last occurrence in
     *     the month.  When in doubt the **last** occurrence is safer to use than the 5th, which may lead to the
     *     next month (see tests).  <br>***Be VERY careful when using relative dates as bounding conditions they may
     *     not consistently occur in the order you expect***
     * @param int|null $year Optional, The year, if **null** the current year is used
     *
     * @throws InvalidArgumentException if dayOfWeek or weekOfMonth are not set
     * @return DateTime a DateTime object with the time set to 00:00:00
     *
     * @see DateTimeUtilTest::getRelativeDateTime()
     * @see DayOfWeek
     * @see Week
     */
    public static function getRelativeDateTime($month, $dayOfWeek, $weekOfMonth, $year = null) {
        $year = (null === $year) ? date('Y') : $year;
        $month = (null === $month) ? date('m') : $month;

        if(null === $dayOfWeek || $dayOfWeek < DayOfWeek::SUNDAY || $dayOfWeek > DayOfWeek::SATURDAY)
            throw new InvalidArgumentException('dayOfWeek must be set and be between ' . DayOfWeek::SUNDAY . ' and ' . DayOfWeek::SATURDAY);
        if(null === $weekOfMonth || $weekOfMonth < Week::LAST || $weekOfMonth > Week::FIFTH)
            throw new InvalidArgumentException('weekOfMonth must be set and be between ' . Week::LAST . ' and ' . Week::FIFTH);

        $date = DateTime::createFromFormat(self::DATETIME_FORMAT, $year . "-" . $month . "-01 00:00");

        if(false === $date) throw new InvalidArgumentException('Invalid date format');

        $mapOccurrence = [
            Week::FIRST => 'first',
            Week::SECOND => 'second',
            Week::THIRD => 'third',
            Week::FOURTH => 'fourth',
            Week::FIFTH => 'fifth',
            Week::LAST => 'last'
        ];
        $mapDayOfWeek = [
            DayOfWeek::SUNDAY => 'sunday',
            DayOfWeek::MONDAY => 'monday',
            DayOfWeek::TUESDAY => 'tuesday',
            DayOfWeek::WEDNESDAY => 'wednesday',
            DayOfWeek::THURSDAY => 'thursday',
            DayOfWeek::FRIDAY => 'friday',
            DayOfWeek::SATURDAY => 'saturday'
        ];
        $occurrence = $mapOccurrence[$weekOfMonth];
        $dayOfWeekName = $mapDayOfWeek[$dayOfWeek];
        $sMonth = $date->format('F');
        $date->modify(strtolower(sprintf('%s %s of %s %d', $occurrence, $dayOfWeekName, $sMonth, $year)));

        return $date;
    }

    /**
     * @param int|null $month
     * @param int|null $day
     * @param int|null $year
     * @return DateTime|false
     */
    public static function getAbsoluteDateTime($month = null, $day = null, $year = null){
        $year = (null === $year) ? date('Y') : $year;
        $month = (null === $month) ? date('m') : $month;
        $day = (null === $day) ? date('d') : $day;

        if ($day === self::RELATIVE_DAY_LAST) { // last day of the month is next month's 0 day
            $month += 1;
            $day = 0;
        }
        $date = DateTime::createFromFormat(self::DATETIME_FORMAT, $year . "-" . $month . "-" . $day . " 00:00");
        if(false === $date) throw new InvalidArgumentException('Invalid date format');
        return $date;
    }

    public static function usFederalHolidays() {
        return [
            Holiday::create(
                self::getAbsoluteDateTime(1, 1),
                'New Year\'s Day',
                'New Year\'s Day',
                'New Year\'s Day is the first day of the Gregorian calendar.'
            ),
            Holiday::create(
                self::getRelativeDateTime(1, DayOfWeek::MONDAY, Week::THIRD),
                'Martin Luther King, Jr. Day',
                'MLK, Jr. Day',
                'Martin Luther King Day marks the anniversary of the date of birth of the influential American civil right leader of the same name.'
            ),
            Holiday::create(
                self::getRelativeDateTime(2, DayOfWeek::MONDAY, Week::THIRD),
                'Presidents\' Day',
                'President\'s Day',
                'Washington\'s Birthday, or Presidents\' Day, honors the life and work of the first president of the United States, George Washington.'
            ),
            Holiday::create(
                self::getRelativeDateTime(5, DayOfWeek::MONDAY, Week::FIRST),
                'Memorial Day',
                'Memorial Day',
                'Memorial Day commemorates all Americans who have died in military service for the United States.'
            ),
            Holiday::create(
                self::getAbsoluteDateTime(6, 19),
                'Juneteenth National Freedom Day',
                'Juneteenth',
                'Juneteenth National Freedom Day is a state observance in the USA.'
            ),
            Holiday::create(
                self::getAbsoluteDateTime(7, 4),
                'Independence Day',
                'Independence Day',
                'On Independence Day, Americans celebrate the anniversary of publication of the Declaration of Independence from Great Britain in 1776.'
            ),
            Holiday::create(
                self::getRelativeDateTime(9, DayOfWeek::MONDAY, Week::FIRST),
                'Labor Day',
                'Labor Day',
                'Labor Day is a federal holiday in the United States. It gives workers a day of rest and it celebrates their contribution to the American economy.'
            ),
            Holiday::create(
                self::getRelativeDateTime(10, DayOfWeek::MONDAY, Week::SECOND),
                'Columbus Day',
                'Columbus Day',
                'Columbus Day celebrates 15th century explorer Christopher Columbus\'s arrival in America in 1492.'
            ),
            Holiday::create(
                self::getAbsoluteDateTime(11, 11),
                'Veterans Day',
                'Veterans Day',
                'Veterans Day in the USA is a holiday to honor all who have served in the United States Military Services.'
            ),
            Holiday::create(
                self::getRelativeDateTime(11, DayOfWeek::THURSDAY, Week::FOURTH),
                'Thanksgiving Day',
                'Thanksgiving Day',
                'Thanksgiving Day in the United States is traditionally a holiday to give thanks for the food collected at the end of the harvest season.'
            ),
            Holiday::create(
                self::getAbsoluteDateTime(12, 25),
                'Christmas Day',
                'Christmas Day',
                'Christmas Day celebrates Jesus Christ\'s birth.'
            ),
        ];
    }
}