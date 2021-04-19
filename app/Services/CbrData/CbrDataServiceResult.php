<?php

namespace App\Services\CbrData;

use DateTime;

/**
 * Class CbrDataServiceResult
 * @package App\Services\CbrData
 */
class CbrDataServiceResult
{
    /**
     * @var DateTime
     */
    private $tradeDay;

    /**
     * @var float
     */
    private $course;

    /**
     * @var DateTime
     */
    private $previousTradeDay;

    /**
     * @var float
     */
    private $previousTradeDayCourseDiff;

    public function __construct(
        DateTime $tradeDay,
        float $course,
        DateTime $previousTradeDay,
        float $previousTradeDayCourseDiff
    ) {
        $this->tradeDay = $tradeDay;
        $this->course = $course;
        $this->previousTradeDay = $previousTradeDay;
        $this->previousTradeDayCourseDiff = $previousTradeDayCourseDiff;
    }

    /**
     * @return DateTime
     */
    public function getTradeDay(): DateTime
    {
        return $this->tradeDay;
    }

    /**
     * @return float
     */
    public function getCourse(): float
    {
        return $this->course;
    }

    /**
     * @return DateTime
     */
    public function getPreviousTradeDay(): DateTime
    {
        return $this->previousTradeDay;
    }

    /**
     * @return float
     */
    public function getPreviousTradeDayCourseDiff(): float
    {
        return $this->previousTradeDayCourseDiff;
    }
}
