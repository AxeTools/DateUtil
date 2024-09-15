<?php

namespace AxeTools\Utilities\DateTime;

use DateTime;

/**
 * A class for representing named holiday's.
 *
 * @since 1.0.0
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
     * @var string|null An abbreviated name of the holiday if there is one, otherwise the same as the long name
     */
    protected $shortName;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var bool
     */
    protected $observedShift;

    /**
     * @param string|null $shortName     Optional abbreviated name of the holiday
     * @param string|null $description   Optional description of the holiday
     * @param bool        $observedShift optional, is the observance of the holiday allowed to shift to an adjacent weekday?  Typically only
     *                                   allowed on holidays that have an absolute date that they fall on to allow them to still be observed when they fall on a weekend
     */
    public function __construct(DateTime $datetime, string $longName, ?string $shortName = null, ?string $description = null, bool $observedShift = false) {
        $this->datetime = $datetime;
        $this->longName = $longName;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->observedShift = $observedShift;
    }

    /**
     * @param bool $observedShift optional, is the observance of the holiday allowed to shift to an adjacent weekday?  Typically only
     *                            allowed on holidays that have an absolute date that they fall on to allow them to still be observed when they fall on a weekend
     *
     * @return self
     */
    public static function create(DateTime $dateTime, string $longName, ?string $shortName = null, ?string $description = null, bool $observedShift = false): Holiday {
        return new self($dateTime, $longName, $shortName, $description, $observedShift);
    }

    public function getDatetime(): DateTime {
        return $this->datetime;
    }

    /**
     * @param DateTime $datetime
     */
    public function setDatetime($datetime): Holiday {
        $this->datetime = $datetime;

        return $this;
    }

    public function getLongName(): string {
        return $this->longName;
    }

    public function setLongName(string $longName): Holiday {
        $this->longName = $longName;

        return $this;
    }

    /**
     * @return string if a short name is not set this will return the long name
     */
    public function getShortName(): ?string {
        return (null === $this->shortName) ? $this->getLongName() : $this->shortName;
    }

    public function setShortName(?string $shortName): Holiday {
        $this->shortName = $shortName;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): Holiday {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool is the Holiday is allowed to be shifted for observance
     */
    public function isObservedShift(): bool {
        return $this->observedShift;
    }

    /**
     * @param bool $observedShift is the Holiday is allowed to be shifted for observance
     */
    public function setObservedShift(bool $observedShift): Holiday {
        $this->observedShift = $observedShift;

        return $this;
    }
}
