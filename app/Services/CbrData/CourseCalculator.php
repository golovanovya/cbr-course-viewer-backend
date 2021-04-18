<?php
declare(strict_types=1);

namespace App\Services\CbrData;

use App\Services\CbrData\Exceptions\CbrDataExternalException;

/**
 * Class CourseCalculator
 * @package App\Services\CbrData
 */
class CourseCalculator
{
    /**
     * @param CurrencyEnum $targetCurrency
     * @param CurrencyEnum $baseCurrency
     * @param CurrencyCourse[] $currencyCourses
     * @return float
     * @throws CbrDataExternalException
     */
    public function calculate(CurrencyEnum $targetCurrency, CurrencyEnum $baseCurrency, array $currencyCourses): float
    {
        $cbrTargetValue = null;
        $cbrBaseValue = null;
        foreach ($currencyCourses as $currencyCourse) {
            if ($currencyCourse->getCurrencyEnum()->equals($targetCurrency)) {
                $cbrTargetValue = $currencyCourse->getCourse() / $currencyCourse->getNominal();
            }
            if ($currencyCourse->getCurrencyEnum()->equals($baseCurrency)) {
                $cbrBaseValue = $currencyCourse->getCourse() / $currencyCourse->getNominal();
            }
        }
        if ($cbrTargetValue === null) {
            throw new CbrDataExternalException(
                'Course not existed for currency ' . $targetCurrency->getValue() . ' on given date'
            );
        }
        if ($cbrBaseValue === null) {
            throw new CbrDataExternalException(
                'Course not existed for currency ' . $baseCurrency->getValue() . ' on given date'
            );
        }

        return $cbrTargetValue / $cbrBaseValue;
    }
}
