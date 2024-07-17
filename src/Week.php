<?php

namespace AxeTools\Utilities\DateTime;

class Week {

    const FIRST = 1;
    const SECOND = 2;
    const THIRD = 3;
    const FOURTH = 4;
    const FIFTH = 5;
    const LAST = -1;

    /**
     * @param int $week numeric week of the month 1 - first to 5 - fifth and -1 for last
     *
     * @return string lowercase english name of the week in the month
     */
    public static function mapToName($week) {
        $mapOccurrence = [
            Week::FIRST => 'first',
            Week::SECOND => 'second',
            Week::THIRD => 'third',
            Week::FOURTH => 'fourth',
            Week::FIFTH => 'fifth',
            Week::LAST => 'last'
        ];
        return $mapOccurrence[$week];
    }
}