<?php

namespace AxeTools\Utilities\DateTime\Tests;

use AxeTools\Utilities\DateTime\DateTimeUtil;
use AxeTools\Utilities\DateTime\DayOfWeek;
use AxeTools\Utilities\DateTime\Holiday;
use AxeTools\Utilities\DateTime\Week;
use DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeUtilTest extends TestCase {

    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @test
     * @dataProvider getRelativeDateTimeDataProvider
     *
     * @param int|null $month
     * @param int|null $day
     * @param int|null $weekOfMonth
     * @param int|null $year
     * @param DateTime $expected
     *
     * @return void
     */
    public function getRelativeDateTime($month, $day, $weekOfMonth, $year, DateTime $expected) {
        $actual = DateTimeUtil::getRelativeDateTime($month, $day, $weekOfMonth, $year);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getAbsoluteDataTimeDataProvider
     *
     * @param int|null $month
     * @param int|null $day
     * @param int|null $year
     * @param DateTime $expected
     *
     * @return void
     */
    public function getAbsoluteDateTime($month, $day, $year, DateTime $expected) {
        $actual = DateTimeUtil::getAbsoluteDateTime($month, $day, $year);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider isHolidayDataProvider
     * @param          $holidays
     * @param DateTime $testDate
     * @param          $options
     * @param          $expected
     *
     * @return void
     */
    public function isHoliday($holidays, DateTime $testDate, $options, $expected){
        $actual = DateTimeUtil::isHoliday($holidays, $options, $testDate);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider isBetweenDataProvider
     * @param $start
     * @param $end
     * @param $checkTime
     * @param $inclusive
     * @param $expected
     * @return void
     */
    public function isBetween($start, $end, $checkTime, $inclusive, $expected) {
        $actual = DateTimeUtil::isBetween($start, $end, $checkTime, $inclusive);
        $this->assertEquals($expected, $actual);
    }

    public function isBetweenDataProvider()
    {
        return [
            'Before' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 11:00:00'),
                true,
                false
            ],
            'At Start' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                true,
                true
            ],
            'At Start Exclusive' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                false,
                false
            ],
            'At Start Exclusive after' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:01'),
                false,
                true
            ],
            'Between' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:10:00'),
                true,
                true
            ],
            'At End' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                true,
                true
            ],
            'At End Exclusive' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                false,
                false
            ],
            'After' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:10:00'),
                true,
                false
            ]
        ];
    }


    public static function getRelativeDateTimeDataProvider() {
        return [
            'Relative Date, first Monday in January 2022' => [1, DayOfWeek::MONDAY, Week::FIRST, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-03 00:00:00')],
            'Relative Date, last Monday in January 2022' => [1, DayOfWeek::MONDAY, Week::LAST, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-31 00:00:00')],
            'Relative Date, 5th Monday in January 2022' => [1, DayOfWeek::MONDAY, Week::FIFTH, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-31 00:00:00')],

            /*
             * Currently the "5th" occurrence logic will go to the next week, which can be the next month.  This can cause some
             * unexpected behavior.  For example February in 2022, the month starts on Tuesday so the "5th" Tuesday is March 1st
             * however the "5th" Monday is March 7th! This is NOT the same for February in 2021 which starts on a Monday
             * For February 2021 the 5th Monday is March 1st and the 5th Tuesday is March 2nd.
             *
             * Therefor NEVER use 5th when you mean last, and do not depend on this behavior it may be patched to ensure 5th never spills to the next month.
             *
             * But Remember that all the occurrence logic can have a similar logic error!
             * For July 2024
             * the "last" Monday is on Monday, July 29th 2024
             * BUT
             * the "last" Friday is on Friday, July 26th 2024 which is BEFORE the last Monday
             *
             */
            '5th Tuesday in a month w/o a 5th Tuesday example 1' => [2, DayOfWeek::TUESDAY, Week::FIFTH, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-03-01 00:00:00')],
            '5th Monday in a month w/o a 5th Monday example 1' => [2, DayOfWeek::MONDAY, Week::FIFTH, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-03-07 00:00:00')],
            '5th Monday in a month w/o a 5th Monday example 2' => [2, DayOfWeek::MONDAY, Week::FIFTH, 2021, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2021-03-01 00:00:00')],
            '5th Tuesday in a month w/o a 5th Tuesday example 2' => [2, DayOfWeek::TUESDAY, Week::FIFTH, 2021, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2021-03-02 00:00:00')],
        ];
    }

    public static function getAbsoluteDataTimeDataProvider() {
        return [
            'full pass through, today' => [null, null, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y-m-d 00:00:00'))],
            'Absolute Date, April 6th 2000' => [4, 6, 2000, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-04-06 00:00:00')],
            'Absolute Date, April 6th this year' => [4, 6, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y') . '-04-06 00:00:00')],
            // not sure why you would need this but the interface allows for it
            'Absolute Date, April current day this year' => [4, null, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y') . '-04-' . date('d') . ' 00:00:00')],

            // Last day of month checks
            'Last day of April 2022' => [4, DateTimeUtil::RELATIVE_DAY_LAST, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-04-30 00:00:00')],
            'Last day of April this year' => [4, DateTimeUtil::RELATIVE_DAY_LAST, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y') . '-04-30 00:00:00')],
            'Last day of the current month' => [null, DateTimeUtil::RELATIVE_DAY_LAST, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y-m-t') . ' 00:00:00')],

            // Roll over date, you use a day of the month that exceeds that months' days
            'Roll over date, day overload leap year' => [2, 31, 2000, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-03-02 00:00:00')],
            'Roll over date, day overload' => [2, 31, 2001, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2001-03-03 00:00:00')],

            // Roll over date, you use a month that exceeds the months' of the year
            'Roll over date, month overload' => [13, 31, 2001, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2002-01-31 00:00:00')],
           ];
    }

    public static function isHolidayDataProvider() {
        return [
            'No Holidays given' => [
                [],
                DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y-m-d 00:00:00')),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                false
            ],

            /* New Years Day in 2022 was on a Saturday */
            'US Holidays given, New Years 2022 (saturday) No Observance' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                true
            ],
            'US Holidays given, New Years 2022 (saturday) Saturday, Sunday Observance On' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                true
            ],
            'US Holidays given, New Years 2022 (saturday) Saturday, Saturday Observance On on Holiday' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                false
            ],
            'US Holidays given, New Years 2022 (saturday) Saturday, Saturday Observance On on Observed' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2021-12-31 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true
            ],

            /* New Years Day in 2023 was on a Sunday */
            'US Holidays given, New Years 2023 (sunday) No Observance' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                true
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, Saturday Observance On' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, Sunday Observance On on Holiday' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                false
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, Sunday Observance On on Observed' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-02 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                true
            ],

            'US Holidays given, New Years 2023 (sunday) Sunday, BOTH Observance On on Holiday' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY | DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                false
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, BOTH Observance On on Observed' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-02 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY | DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true
            ],

            'Father\'s day an unobservable holiday, both observances on' => [
                [
                    Holiday::create(
                        DateTimeUtil::getRelativeDateTime(6, DayOfWeek::SUNDAY, Week::THIRD, 2000),
                        'Father\'s day',
                        'Father\'s day',
                        '',
                        false
                    )
                ],
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-06-18 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY | DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true
            ],
            'Father\'s day an unobservable holiday, Sunday observance on' => [
                [
                    Holiday::create(
                        DateTimeUtil::getRelativeDateTime(6, DayOfWeek::SUNDAY, Week::THIRD, 2000),
                        'Father\'s day',
                        'Father\'s day',
                        '',
                        false
                    )
                ],
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-06-18 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                true
            ],
            'Father\'s day an unobservable holiday, no observance on' => [
                [
                    Holiday::create(
                        DateTimeUtil::getRelativeDateTime(6, DayOfWeek::SUNDAY, Week::THIRD, 2000),
                        'Father\'s day',
                        'Father\'s day',
                        '',
                        false
                    )
                ],
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-06-18 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                true
            ],

        ];
    }
}