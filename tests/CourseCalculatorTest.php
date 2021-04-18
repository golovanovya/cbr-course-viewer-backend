<?php

use App\Services\CbrData\CourseCalculator;
use App\Services\CbrData\CurrencyCourse;
use App\Services\CbrData\CurrencyEnum;
use App\Services\CbrData\Exceptions\CbrDataExternalException;

/**
 * Class CourseCalculatorTest
 */
class CourseCalculatorTest extends TestCase
{
    /**
     * @var CourseCalculator
     */
    private $courseCalculator;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->courseCalculator = new CourseCalculator();
    }

    public function testCourseIsCorrect()
    {
        $course = $this->courseCalculator->calculate(
            CurrencyEnum::USD(),
            CurrencyEnum::EUR(),
            [
                new CurrencyCourse(CurrencyEnum::USD(), 1, 75),
                new CurrencyCourse(CurrencyEnum::EUR(), 1, 90),
            ]
        );
        $this->assertEqualsWithDelta(0.83333, $course, 0.00001);

        $course = $this->courseCalculator->calculate(
            CurrencyEnum::USD(),
            CurrencyEnum::AMD(),
            [
                new CurrencyCourse(CurrencyEnum::USD(), 1, 75),
                new CurrencyCourse(CurrencyEnum::AMD(), 100, 14),
            ]
        );
        $this->assertEqualsWithDelta(535.71428, $course, 0.00001);

        $course = $this->courseCalculator->calculate(
            CurrencyEnum::HKD(),
            CurrencyEnum::AMD(),
            [
                new CurrencyCourse(CurrencyEnum::HKD(), 10, 86),
                new CurrencyCourse(CurrencyEnum::AMD(), 100, 14),
            ]
        );
        $this->assertEqualsWithDelta(61.42857, $course, 0.00001);
    }

    public function testExceptionOnCoursesDoesntHaveTargetCurrency()
    {
        $this->expectException(CbrDataExternalException::class);
        $this->courseCalculator->calculate(
            CurrencyEnum::USD(),
            CurrencyEnum::EUR(),
            [
                new CurrencyCourse(CurrencyEnum::RUR(), 1, 75),
                new CurrencyCourse(CurrencyEnum::EUR(), 1, 90),
            ]
        );
    }

    public function testExceptionOnCoursesDoesntHaveBaseCurrency()
    {
        $this->expectException(CbrDataExternalException::class);
        $this->courseCalculator->calculate(
            CurrencyEnum::USD(),
            CurrencyEnum::EUR(),
            [
                new CurrencyCourse(CurrencyEnum::USD(), 1, 75),
                new CurrencyCourse(CurrencyEnum::RUR(), 1, 90),
            ]
        );
    }
}
