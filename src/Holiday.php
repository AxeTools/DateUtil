<?php

namespace AxeTools\Utilities\DateTime;

use DateTimeInterface;

class Holiday {

    protected $datetime;
    protected $longName;
    protected $shortName;
    protected $description;

    /**
     * @param DateTimeInterface $datetime
     * @param string            $longName
     * @param string            $shortName
     * @param string|null       $description
     */
    public function __construct(DateTimeInterface $datetime, $longName, $shortName, $description = null){
        $this->datetime = $datetime;
        $this->longName = $longName;
        $this->shortName = $shortName;
        $this->description = $description;
    }

    /**
     * @param DateTimeInterface $dateTime
     * @param string            $longName
     * @param string            $shortName
     * @param string|null       $description
     *
     * @return self
     */
    public static function create(DateTimeInterface $dateTime, $longName, $shortName, $description = null){
        return new self($dateTime, $longName, $shortName, $description);
    }

    /**
     * @return DateTimeInterface
     */
    public function getDatetime() {
        return $this->datetime;
    }

    /**
     * @param DateTimeInterface $datetime
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
     * @return string
     */
    public function getShortName() {
        return $this->shortName;
    }

    /**
     * @param string $shortName
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



}
