<?php

namespace App\Services\CbrData;

use DateTime;
use Exception;
use Psr\SimpleCache\CacheInterface;
use UnexpectedValueException;

/**
 * Class CbrDataService
 * @package App\Services\CbrData
 */
class CbrDataService
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var CbrDataProvider
     */
    private $dataProvider;

    /**
     * CbrDataService constructor.
     * @param CacheInterface $cache
     * @param CbrDataProvider $dataProvider
     */
    public function __construct(CacheInterface $cache, CbrDataProvider $dataProvider)
    {
        $this->cache = $cache;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param string $targetCurrency
     * @param string $baseCurrency
     * @param string $date
     * @return float
     * @throws CbrDataException
     */
    public function getCourseOnDate(string $targetCurrency, string $baseCurrency, string $date): float
    {
        $targetCurrencyEnum = $this->getCurrencyEnumByISOCode($targetCurrency);
        $baseCurrencyEnum = $this->getCurrencyEnumByISOCode($baseCurrency);
        $dateTime = $this->getDateTimeByDateString($date);



        return 2.23456;
    }

    /**
     * @param string $currencyCode ISO char code
     * @return CurrencyEnum
     * @throws CbrDataException
     */
    private function getCurrencyEnumByISOCode(string $currencyCode): CurrencyEnum
    {
        try {
            return new CurrencyEnum($currencyCode);
        } catch (UnexpectedValueException $e) {
            throw new CbrDataException("Incorrect ISO value - {$currencyCode}", $e->getCode(), $e);
        }
    }

    /**
     * @param string $date
     * @return DateTime
     * @throws CbrDataException
     */
    private function getDateTimeByDateString(string $date): DateTime
    {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            throw new CbrDataException("Incorrect date value - {$date}, allowed format is \"YYYY-MM-DD\"");
        }

        try {
            $dateTime = new DateTime($date);
            $dateTime->setTime(0, 0, 0, 0);
        } catch (Exception $e) {
            throw new CbrDataException($e->getMessage(), $e->getCode(), $e);
        }

        $now = new DateTime('now');
        if ($dateTime > $now) {
            throw new CbrDataException("Incorrect date value - {$date}, date is in future");
        }

        return $dateTime;
    }
}
