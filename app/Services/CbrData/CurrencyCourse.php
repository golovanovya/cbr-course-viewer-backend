<?php
declare(strict_types=1);

namespace App\Services\CbrData;

use App\Services\CbrData\Exceptions\CbrDataInternalException;

/**
 * Class CbrDataDTO
 * @package App\Services\CbrData
 */
class CurrencyCourse
{
    /**
     * @var CurrencyEnum
     */
    private $currencyEnum;

    /**
     * @var int
     */
    private $nominal;

    /**
     * @var float
     */
    private $course;

    /**
     * CurrencyCourse constructor.
     * @param CurrencyEnum $currencyEnum
     * @param int $nominal
     * @param float $course
     * @throws CbrDataInternalException
     */
    public function __construct(CurrencyEnum $currencyEnum, int $nominal, float $course)
    {
        if ($nominal <= 0) {
            throw new CbrDataInternalException(
                "Incorrect nominal {$nominal} for currency {$currencyEnum->getValue()}"
            );
        }
        if ($course <= 0) {
            throw new CbrDataInternalException(
                "Incorrect course {$course} for currency {$currencyEnum->getValue()}"
            );
        }

        $this->currencyEnum = $currencyEnum;
        $this->nominal = $nominal;
        $this->course = $course;
    }

    /**
     * @return CurrencyEnum
     */
    public function getCurrencyEnum(): CurrencyEnum
    {
        return $this->currencyEnum;
    }

    /**
     * @return int
     */
    public function getNominal(): int
    {
        return $this->nominal;
    }

    /**
     * @return float
     */
    public function getCourse(): float
    {
        return $this->course;
    }
}
