<?php
declare(strict_types=1);

namespace App\Services\CbrData;

use App\Services\CbrData\Exceptions\CbrDataExternalException;
use App\Services\CbrData\Exceptions\CbrDataInternalException;
use DateTime;
use Exception;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class CbrDataService
 * @package App\Services\CbrData
 */
class CbrDataService
{
    private const CACHE_PREFIX = 'CbrDataService_';

    /**
     * @var CbrDataProvider
     */
    private $dataProvider;

    /**
     * @var CourseCalculator
     */
    private $courseCalculator;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * CbrDataService constructor.
     * @param CbrDataProvider $dataProvider
     * @param CourseCalculator $courseCalculator
     * @param CacheInterface $cache
     */
    public function __construct(
        CbrDataProvider $dataProvider,
        CourseCalculator $courseCalculator,
        CacheInterface $cache
    ) {
        $this->dataProvider = $dataProvider;
        $this->courseCalculator = $courseCalculator;
        $this->cache = $cache;
    }

    /**
     * Returns target currency course by base currency on specific date
     * @param string $targetCurrencyISOCode
     * @param string $baseCurrencyISOCode
     * @param string $date
     * @return float
     * @throws CbrDataExternalException|CbrDataInternalException
     */
    public function getCourseOnDate(string $targetCurrencyISOCode, string $baseCurrencyISOCode, string $date): float
    {
        $targetCurrency = $this->getCurrencyEnumByISOCode($targetCurrencyISOCode);
        $baseCurrency = $this->getCurrencyEnumByISOCode($baseCurrencyISOCode);
        $dateTime = $this->getDateTimeByDateString($date);

        $currencyCourses = $this->getCurrencyCoursesOnDate($dateTime);
        $currencyCourses[] = new CurrencyCourse(
            CurrencyEnum::RUR(),
            1,
            1
        );

        return $this->courseCalculator->calculate(
            $targetCurrency,
            $baseCurrency,
            $currencyCourses
        );
    }

    /**
     * Returns currency enum by currency ISO code
     * @param string $currencyCode ISO char code
     * @return CurrencyEnum
     * @throws CbrDataExternalException
     */
    private function getCurrencyEnumByISOCode(string $currencyCode): CurrencyEnum
    {
        try {
            return new CurrencyEnum($currencyCode);
        } catch (UnexpectedValueException $e) {
            throw new CbrDataExternalException("Incorrect ISO value - {$currencyCode}", $e->getCode(), $e);
        }
    }

    /**
     * Returns datetime object by given date string (in past or today) in YYYY-MM-DD format
     * @param string $date
     * @return DateTime
     * @throws CbrDataExternalException
     */
    private function getDateTimeByDateString(string $date): DateTime
    {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            throw new CbrDataExternalException("Incorrect date value - {$date}, allowed format is \"YYYY-MM-DD\"");
        }

        try {
            $dateTime = new DateTime($date);
            $dateTime->setTime(0, 0, 0, 0);
        } catch (Exception $e) {
            throw new CbrDataExternalException($e->getMessage(), $e->getCode(), $e);
        }

        $now = new DateTime('now');
        if ($dateTime > $now) {
            throw new CbrDataExternalException("Incorrect date value - {$date}, date is in future");
        }

        return $dateTime;
    }

    /**
     * Returns currency course on specific date using cache
     * @param DateTime $dateTime
     * @return CurrencyCourse[]
     * @throws CbrDataInternalException
     */
    private function getCurrencyCoursesOnDate(DateTime $dateTime): array
    {
        $cacheKey = self::CACHE_PREFIX . '_' . $dateTime->format('Y-m-d');
        try {
            $serializedData = $this->cache->get($cacheKey);
        } catch (InvalidArgumentException $e) {
            throw new CbrDataInternalException($e->getMessage(), $e->getCode(), $e);
        }

        if ($serializedData) {
            return unserialize($serializedData);
        }

        $currencyCourses = $this->dataProvider->getCurrencyCoursesOnDate($dateTime);
        try {
            $this->cache->set($cacheKey, serialize($currencyCourses));
        } catch (InvalidArgumentException $e) {
            throw new CbrDataInternalException($e->getMessage(), $e->getCode(), $e);
        }
        return $currencyCourses;
    }
}
