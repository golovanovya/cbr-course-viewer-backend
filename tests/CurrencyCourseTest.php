<?php

use App\Services\CbrData\CurrencyCourse;
use App\Services\CbrData\CurrencyEnum;
use App\Services\CbrData\Exceptions\CbrDataInternalException;

/**
 * Class CurrencyCourseTest
 */
class CurrencyCourseTest extends TestCase
{
    public function testConstructIsCorrect()
    {
        $currencyCourse = new CurrencyCourse(CurrencyEnum::USD(), 1, 75);
        $this->assertInstanceOf(CurrencyEnum::class, $currencyCourse->getCurrencyEnum());
        $this->assertTrue($currencyCourse->getCurrencyEnum()->equals(CurrencyEnum::USD()));
        $this->assertEquals(1, $currencyCourse->getNominal());
        $this->assertEqualsWithDelta(75, $currencyCourse->getCourse(), 0.00001);
    }

    public function testExceptionOnNegativeNominal()
    {
        $this->expectException(CbrDataInternalException::class);
        new CurrencyCourse(CurrencyEnum::USD(), -1, 75);
    }

    public function testExceptionOnZeroNominal()
    {
        $this->expectException(CbrDataInternalException::class);
        new CurrencyCourse(CurrencyEnum::USD(), 0, 75);
    }

    public function testExceptionOnNegativeCourse()
    {
        $this->expectException(CbrDataInternalException::class);
        new CurrencyCourse(CurrencyEnum::USD(), 1, -10);
    }

    public function testExceptionOnZeroCourse()
    {
        $this->expectException(CbrDataInternalException::class);
        new CurrencyCourse(CurrencyEnum::USD(), 1, 0);
    }
}
