<?php

namespace AxeTools\Utilities\DateTime\Tests;

use AxeTools\Utilities\DateTime\DateTimeUtil;
use AxeTools\Utilities\DateTime\DayOfWeek;
use AxeTools\Utilities\DateTime\Holiday;
use AxeTools\Utilities\DateTime\Week;
use DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeUtilTest extends TestCase {
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @test
     * @dataProvider createRelativeDateTimeDataProvider
     *
     * @return void
     */
    public function createRelativeDateTime(?int $month, ?int $day, ?int $weekOfMonth, ?int $year, DateTime $expected) {
        $actual = DateTimeUtil::createRelativeDateTime($month, $day, $weekOfMonth, $year);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider createAbsoluteDataTimeDataProvider
     *
     * @return void
     */
    public function createAbsoluteDateTime(?int $month, ?int $day, ?int $year, DateTime $expected) {
        $actual = DateTimeUtil::createAbsoluteDateTime($month, $day, $year);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider isHolidayDataProvider
     *
     * @param array<Holiday> $holidays
     *
     * @return void
     */
    public function isHoliday(array $holidays, DateTime $testDate, int $options, bool $expected) {
        $actual = DateTimeUtil::isHoliday($holidays, $testDate, $options);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider isBetweenDataProvider
     *
     * @return void
     */
    public function isBetween(DateTime $start, DateTime $end, DateTime $checkTime, int $options, bool $expected) {
        $actual = DateTimeUtil::isBetween($start, $end, $checkTime, $options);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed>
     */
    public static function isBetweenDataProvider(): array {
        return [
            'Before' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 11:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE,
                false,
            ],
            'At Start' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE,
                true,
            ],
            'At Start Inclusive Start' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE_START,
                true,
            ],
            'At Start Inclusive End' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE_END,
                false,
            ],
            'At Start Exclusive' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                DateTimeUtil::IS_BETWEEN_EXCLUSIVE,
                false,
            ],
            'At Start Exclusive after' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:01'),
                DateTimeUtil::IS_BETWEEN_EXCLUSIVE,
                true,
            ],
            'Between' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:10:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE,
                true,
            ],
            'At End' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE,
                true,
            ],
            'At End Exclusive' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                DateTimeUtil::IS_BETWEEN_EXCLUSIVE,
                false,
            ],
            'At End Inclusive End' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE_END,
                true,
            ],
            'At End Inclusive Start' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE_START,
                false,
            ],
            'After' => [
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 12:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:00:00'),
                \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-12-02 13:10:00'),
                DateTimeUtil::IS_BETWEEN_INCLUSIVE,
                false,
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function createRelativeDateTimeDataProvider(): array {
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

    /**
     * @return array<mixed>
     */
    public static function createAbsoluteDataTimeDataProvider(): array {
        return [
            'full pass through, today' => [null, null, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y-m-d 00:00:00'))],
            'Absolute Date, April 6th 2000' => [4, 6, 2000, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-04-06 00:00:00')],
            'Absolute Date, April 6th this year' => [4, 6, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y').'-04-06 00:00:00')],
            // not sure why you would need this but the interface allows for it
            'Absolute Date, April current day this year' => [4, null, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y').'-04-'.date('d').' 00:00:00')],

            // Last day of month checks
            'Last day of April 2022' => [4, DateTimeUtil::RELATIVE_DAY_LAST, 2022, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-04-30 00:00:00')],
            'Last day of April this year' => [4, DateTimeUtil::RELATIVE_DAY_LAST, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y').'-04-30 00:00:00')],
            'Last day of the current month' => [null, DateTimeUtil::RELATIVE_DAY_LAST, null, \DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y-m-t').' 00:00:00')],

            // Roll over date, you use a day of the month that exceeds that months' days
            'Roll over date, day overload leap year' => [2, 31, 2000, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-03-02 00:00:00')],
            'Roll over date, day overload' => [2, 31, 2001, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2001-03-03 00:00:00')],

            // Roll over date, you use a month that exceeds the months' of the year
            'Roll over date, month overload' => [13, 31, 2001, \DateTime::createFromFormat(self::DATETIME_FORMAT, '2002-01-31 00:00:00')],
           ];
    }

    /**
     * @return array<mixed>
     */
    public static function isHolidayDataProvider(): array {
        return [
            'No Holidays given' => [
                [],
                DateTime::createFromFormat(self::DATETIME_FORMAT, date('Y-m-d 00:00:00')),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                false,
            ],

            /* New Years Day in 2022 was on a Saturday */
            'US Holidays given, New Years 2022 (saturday) No Observance' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                true,
            ],
            'US Holidays given, New Years 2022 (saturday) Saturday, Sunday Observance On' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                true,
            ],
            'US Holidays given, New Years 2022 (saturday) Saturday, Saturday Observance On on Holiday' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2022-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                false,
            ],
            'US Holidays given, New Years 2022 (saturday) Saturday, Saturday Observance On on Observed' => [
                DateTimeUtil::usFederalHolidays(2022),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2021-12-31 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true,
            ],

            /* New Years Day in 2023 was on a Sunday */
            'US Holidays given, New Years 2023 (sunday) No Observance' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                true,
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, Saturday Observance On' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true,
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, Sunday Observance On on Holiday' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                false,
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, Sunday Observance On on Observed' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-02 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                true,
            ],

            'US Holidays given, New Years 2023 (sunday) Sunday, BOTH Observance On on Holiday' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-01 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY | DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                false,
            ],
            'US Holidays given, New Years 2023 (sunday) Sunday, BOTH Observance On on Observed' => [
                DateTimeUtil::usFederalHolidays(2023),
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2023-01-02 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY | DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true,
            ],

            'Father\'s day an unobservable holiday, both observances on' => [
                [
                    Holiday::create(
                        DateTimeUtil::createRelativeDateTime(6, DayOfWeek::SUNDAY, Week::THIRD, 2000),
                        'Father\'s day',
                        'Father\'s day',
                        '',
                        false
                    ),
                ],
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-06-18 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY | DateTimeUtil::HOLIDAY_OBSERVED_SATURDAY_TO_FRIDAY,
                true,
            ],
            'Father\'s day an unobservable holiday, Sunday observance on' => [
                [
                    Holiday::create(
                        DateTimeUtil::createRelativeDateTime(6, DayOfWeek::SUNDAY, Week::THIRD, 2000),
                        'Father\'s day',
                        'Father\'s day',
                        '',
                        false
                    ),
                ],
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-06-18 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_SUNDAY_TO_MONDAY,
                true,
            ],
            'Father\'s day an unobservable holiday, no observance on' => [
                [
                    Holiday::create(
                        DateTimeUtil::createRelativeDateTime(6, DayOfWeek::SUNDAY, Week::THIRD, 2000),
                        'Father\'s day',
                        'Father\'s day',
                        '',
                        false
                    ),
                ],
                DateTime::createFromFormat(self::DATETIME_FORMAT, '2000-06-18 00:00:00'),
                DateTimeUtil::HOLIDAY_OBSERVED_NONE,
                true,
            ],
        ];
    }
}
