<?php
declare(strict_types=1);

namespace App\Services\CbrData;

use App\Services\CbrData\Exceptions\CbrDataInternalException;

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
     * @throws CbrDataInternalException
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
            throw new CbrDataInternalException('Course not existed for currency' . $targetCurrency->getValue());
        }
        if ($cbrBaseValue === null) {
            throw new CbrDataInternalException('Course not existed for currency' . $baseCurrency->getValue());
        }
        if ($cbrTargetValue === 0) {
            throw new CbrDataInternalException('Incorrect course for currency' . $targetCurrency->getValue());
        }
        if ($cbrBaseValue === 0) {
            throw new CbrDataInternalException('Incorrect course for currency' . $baseCurrency->getValue());
        }

        return $cbrTargetValue / $cbrBaseValue;
    }
}
