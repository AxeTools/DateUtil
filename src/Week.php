<?php

namespace AxeTools\Utilities\DateTime;

/**
 * Integer to string conversions for the weeks of a month, including a special integer (-1) to indicate the last week
 * of a month.
 *
 * @since 1.0.0
 */
class Week {
    public const FIRST = 1;
    public const SECOND = 2;
    public const THIRD = 3;
    public const FOURTH = 4;
    public const FIFTH = 5;
    public const LAST = -1;

    /**
     * @param int $week numeric week of the month 1 - first to 5 - fifth and -1 for last
     *
     * @return string lowercase english name of the week in the month
     *
     * @since 1.0.0
     */
    public static function mapToName($week) {
        $mapOccurrence = [
            Week::FIRST => 'first',
            Week::SECOND => 'second',
            Week::THIRD => 'third',
            Week::FOURTH => 'fourth',
            Week::FIFTH => 'fifth',
            Week::LAST => 'last',
        ];

        return $mapOccurrence[$week];
    }
}
