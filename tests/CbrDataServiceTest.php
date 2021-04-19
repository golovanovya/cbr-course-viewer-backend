<?php

use App\Services\CbrData\CbrDataProvider;
use App\Services\CbrData\CbrDataProviderResult;
use App\Services\CbrData\CbrDataService;
use App\Services\CbrData\CourseCalculator;
use App\Services\CbrData\CurrencyCourse;
use App\Services\CbrData\CurrencyEnum;
use App\Services\CbrData\Exceptions\CbrDataExternalException;
use Psr\SimpleCache\CacheInterface;

class CbrDataServiceTest extends TestCase
{
    public function testCourseIsCorrect()
    {
        $service = $this->getService();
        $serviceResult = $service->getCourseOnDate('USD', 'EUR', '2020-02-29');
        $this->assertEqualsWithDelta($serviceResult->getCourse(), 0.83333, 0.00001);
    }

    public function testCachedCourseIsCorrect()
    {
        $service = $this->getService(serialize(new CbrDataProviderResult(
            [
                new CurrencyCourse(CurrencyEnum::USD(), 1, 75),
                new CurrencyCourse(CurrencyEnum::EUR(), 1, 90),
                new CurrencyCourse(CurrencyEnum::RUR(), 1, 1),
            ],
            new DateTime('03.03.2020')
        )));
        $serviceResult = $service->getCourseOnDate('USD', 'EUR', '2020-02-29');
        $this->assertEqualsWithDelta($serviceResult->getCourse(), 0.83333, 0.00001);
    }

    public function testExceptionOnIncorrectISOTargetCurrency()
    {
        $this->expectException(CbrDataExternalException::class);
        $this->getService()->getCourseOnDate('USDD', 'EUR', '2020-02-29');
    }

    public function testExceptionOnIncorrectISOBaseCurrency()
    {
        $this->expectException(CbrDataExternalException::class);
        $this->getService()->getCourseOnDate('USD', 'EURR', '2020-02-29');
    }

    public function testExceptionOnIncorrectDateFormat()
    {
        $this->expectException(CbrDataExternalException::class);
        $this->getService()->getCourseOnDate('USD', 'EUR', '202020-02-29');
    }

    public function testExceptionOnDateInFuture()
    {
        $this->expectException(CbrDataExternalException::class);
        $this->getService()->getCourseOnDate('USD', 'EUR', '2040-02-29');
    }

    private function getService($cacheValue = null)
    {
        $dataProvider = Mockery::mock(CbrDataProvider::class);
        $dataProvider->shouldReceive('getCurrencyCoursesOnDate')->andReturn(new CbrDataProviderResult(
            [
                new CurrencyCourse(CurrencyEnum::USD(), 1, 75),
                new CurrencyCourse(CurrencyEnum::EUR(), 1, 90),
            ],
            new DateTime('03.03.2020')
        ));

        $courseCalculator = Mockery::mock(CourseCalculator::class);
        $courseCalculator->shouldReceive('calculate')->andReturn(0.83333);

        $cache = Mockery::mock(CacheInterface::class);
        $cache->shouldReceive('get')->andReturn($cacheValue);
        $cache->shouldReceive('set');

        return new CbrDataService($dataProvider, $courseCalculator, $cache);
    }
}
