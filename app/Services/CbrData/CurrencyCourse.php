<?php
declare(strict_types=1);

namespace App\Services\CbrData;

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
     * CurrencyCourseDTO constructor.
     * @param CurrencyEnum $currencyEnum
     * @param int $nominal
     * @param float $course
     */
    public function __construct(CurrencyEnum $currencyEnum, int $nominal, float $course)
    {
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
