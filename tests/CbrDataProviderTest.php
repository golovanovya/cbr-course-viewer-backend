<?php

use App\Services\CbrData\CbrDataProvider;
use App\Services\CbrData\CurrencyCourse;
use App\Services\CbrData\CurrencyEnum;
use App\Services\CbrData\Exceptions\CbrDataInternalException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class CbrDataProviderTest extends TestCase
{
    public function testProviderDataIsCorrect()
    {
        $dataProvider = $this->getDataProvider($this->getCorrectXml());
        $currencyCourses = $dataProvider->getCurrencyCoursesOnDate(new DateTime('2020-02-29'));
        $correctCurrencyCourses = $this->getCorrectCurrencyCourses();

        /**
         * @var CurrencyCourse $correctCurrencyCourse
         */
        foreach ($correctCurrencyCourses as $index => $correctCurrencyCourse) {
            /**
             * @var CurrencyCourse $currencyCourse
             */
            $currencyCourse = $currencyCourses[$index];
            $this->assertInstanceOf(CurrencyCourse::class, $currencyCourse);
            $this->assertTrue($correctCurrencyCourse->getCurrencyEnum()->equals($currencyCourse->getCurrencyEnum()));
            $this->assertEquals($correctCurrencyCourse->getNominal(), $currencyCourse->getNominal());
            $this->assertEqualsWithDelta($correctCurrencyCourse->getCourse(), $currencyCourse->getCourse(), 0.00001);
        }
    }

    public function testNotOkResponse()
    {
        $this->expectException(CbrDataInternalException::class);
        $dataProvider = $this->getDataProvider('', 400);
        $dataProvider->getCurrencyCoursesOnDate(new DateTime('2020-02-29'));
    }

    public function testEmptyResponse()
    {
        $this->expectException(CbrDataInternalException::class);
        $dataProvider = $this->getDataProvider('');
        $dataProvider->getCurrencyCoursesOnDate(new DateTime('2020-02-29'));
    }

    public function testEmptyCurrencySet()
    {
        $this->expectException(CbrDataInternalException::class);
        $dataProvider = $this->getDataProvider('<ValCurs Date="29.02.2020" name="Foreign Currency Market"></ValCurs>');
        $dataProvider->getCurrencyCoursesOnDate(new DateTime('2020-02-29'));
    }

    private function getDataProvider($xmlString, $statusCode = 200)
    {
        $clientInterface = $this->getClientInterface($xmlString, $statusCode);
        $requestInterface = $this->getRequestInterface();
        $uriInterface = $this->getUriInterface();

        return new CbrDataProvider($clientInterface, $requestInterface, $uriInterface);
    }

    private function getClientInterface($xmlString = '', $statusCode = 200): ClientInterface
    {
        $clientInterface = Mockery::mock(ClientInterface::class);
        $clientInterface->shouldReceive('sendRequest')->andReturn($this->getResponseInterface($xmlString, $statusCode));
        return $clientInterface;
    }

    private function getRequestInterface(): RequestInterface
    {
        $requestInterface = Mockery::mock(RequestInterface::class);
        $requestInterface->shouldReceive('withUri')->andReturn($requestInterface);
        return $requestInterface;
    }

    private function getResponseInterface($xmlString, $statusCode): ResponseInterface
    {
        $responseInterface = Mockery::mock(ResponseInterface::class);
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('getContents')->andReturn($xmlString);

        $responseInterface->shouldReceive('getStatusCode')->andReturn($statusCode);
        $responseInterface->shouldReceive('getBody')->andReturn($body);
        return $responseInterface;
    }

    private function getUriInterface(): UriInterface
    {
        $uriInterface = Mockery::mock(UriInterface::class);
        $uriInterface->shouldReceive('withScheme')->andReturn($uriInterface);
        $uriInterface->shouldReceive('withHost')->andReturn($uriInterface);
        $uriInterface->shouldReceive('withPath')->andReturn($uriInterface);
        $uriInterface->shouldReceive('withQuery')->andReturn($uriInterface);
        return $uriInterface;
    }

    private function getCorrectXml(): string
    {
        return preg_replace('/\n|\t|\s{2,}/', '', '
<ValCurs Date="29.02.2020" name="Foreign Currency Market">
    <Valute ID="R01010"><NumCode>036</NumCode><CharCode>AUD</CharCode><Nominal>1</Nominal><Name>Австралийский доллар</Name><Value>43,8321</Value></Valute>
    <Valute ID="R01060"><NumCode>051</NumCode><CharCode>AMD</CharCode><Nominal>100</Nominal><Name>Армянских драмов</Name><Value>13,9914</Value></Valute>
    <Valute ID="R01200"><NumCode>344</NumCode><CharCode>HKD</CharCode><Nominal>10</Nominal><Name>Гонконгских долларов</Name><Value>85,9508</Value></Valute>
    <Valute ID="R01235"><NumCode>840</NumCode><CharCode>USD</CharCode><Nominal>1</Nominal><Name>Доллар США</Name><Value>66,9909</Value></Valute>
    <Valute ID="R01239"><NumCode>978</NumCode><CharCode>EUR</CharCode><Nominal>1</Nominal><Name>Евро</Name><Value>73,7235</Value></Valute>
</ValCurs>
        ');
    }

    private function getCorrectCurrencyCourses(): array
    {
        return [
            new CurrencyCourse(CurrencyEnum::AUD(), 1, 43.8321),
            new CurrencyCourse(CurrencyEnum::AMD(), 100, 13.9914),
            new CurrencyCourse(CurrencyEnum::HKD(), 10, 85.9508),
            new CurrencyCourse(CurrencyEnum::USD(), 1, 66.9909),
            new CurrencyCourse(CurrencyEnum::EUR(), 1, 73.7235),
        ];
    }
}
