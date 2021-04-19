<?php

namespace App\Services\CbrData;

use DateTime;

/**
 * Class CbrDataProviderResult
 * @package App\Services\CbrData
 */
class CbrDataProviderResult
{
    /**
     * @var CurrencyCourse[]
     */
    private $currencyCourses;

    /**
     * @var DateTime
     */
    private $tradeDay;

    /**
     * CbrDataProviderResult constructor.
     * @param CurrencyCourse[] $currencyCourses
     * @param DateTime $tradeDay
     */
    public function __construct(array $currencyCourses, DateTime $tradeDay)
    {
        $this->currencyCourses = $currencyCourses;
        $this->tradeDay = $tradeDay;
    }

    /**
     * @return CurrencyCourse[]
     */
    public function getCurrencyCourses(): array
    {
        return $this->currencyCourses;
    }

    /**
     * @return DateTime
     */
    public function getTradeDay(): DateTime
    {
        return $this->tradeDay;
    }
}
