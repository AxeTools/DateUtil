<?php

namespace AxeTools\Utilities\DateTime;

use DateTime;

/**
 * A class for representing named holiday's
 *
 * @since 1.0.0
 * @package AxeTools\DateUtil
 */
class Holiday {

    /**
     * @var DateTime The Actual generated date for the holiday
     */
    protected $datetime;

    /**
     * @var string The full name of the holiday
     */
    protected $longName;
    /**
     * @var string An abbreviated name of the holiday if there is one, otherwise the same as the long name
     */
    protected $shortName;
    protected $description;
    protected $observedShift;

    /**
     * @param DateTime    $datetime
     * @param string      $longName
     * @param string|null $shortName Optional abbreviated name of the holiday
     * @param string|null $description Optional description of the holiday
     * @param bool        $observedShift Optional, is the observance of the holiday allowed to shift to an adjacent weekday?  Typically only
     *  allowed on holidays that have an absolute date that they fall on to allow them to still be observed when they fall on a weekend.
     */
    public function __construct(DateTime $datetime, $longName, $shortName = null, $description = null, $observedShift = false) {
        $this->datetime = $datetime;
        $this->longName = $longName;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->observedShift = $observedShift;
    }

    /**
     * @param DateTime    $dateTime
     * @param string      $longName
     * @param string      $shortName
     * @param string|null $description
     * @param bool $observedShift Optional, is the observance of the holiday allowed to shift to an adjacent weekday?  Typically only
     *   allowed on holidays that have an absolute date that they fall on to allow them to still be observed when they fall on a weekend.
     *
     * @return self
     */
    public static function create(DateTime $dateTime, $longName, $shortName, $description = null, $observedShift = false) {
        return new self($dateTime, $longName, $shortName, $description, $observedShift);
    }

    /**
     * @return DateTime
     */
    public function getDatetime() {
        return $this->datetime;
    }

    /**
     * @param DateTime $datetime
     *
     * @return Holiday
     */
    public function setDatetime($datetime) {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongName() {
        return $this->longName;
    }

    /**
     * @param string $longName
     *
     * @return Holiday
     */
    public function setLongName($longName) {
        $this->longName = $longName;
        return $this;
    }

    /**
     * @return string if a short name is not set this will return the long name
     */
    public function getShortName() {
        return (null === $this->shortName) ? $this->getLongName() : $this->shortName;
    }

    /**
     * @param string|null $shortName
     *
     * @return Holiday
     */
    public function setShortName($shortName) {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Holiday
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool is the Holiday is allowed to be shifted for observance
     */
    public function isObservedShift() {
        return $this->observedShift;
    }

    /**
     * @param bool $observedShift is the Holiday is allowed to be shifted for observance
     *
     * @return Holiday
     */
    public function setObservedShift($observedShift) {
        $this->observedShift = $observedShift;
        return $this;
    }



}
