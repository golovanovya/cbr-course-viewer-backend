<?php
declare(strict_types=1);

namespace App\Services\CbrData;

use App\Services\CbrData\Exceptions\CbrDataInternalException;
use DateTime;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use SimpleXMLElement;
use UnexpectedValueException;

/**
 * Class CbrDataProvider
 * @package App\Services\CbrData
 */
class CbrDataProvider
{
    private const CBR_DATA_SCHEME = 'http';

    private const CBR_DATA_HOST = 'www.cbr.ru';

    private const CBR_DATA_PATH = 'scripts/XML_daily.asp';

    private const ATTEMPTS = 3;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * CbrDataProvider constructor.
     * @param ClientInterface $client
     * @param RequestInterface $request
     * @param UriInterface $uri
     */
    public function __construct(ClientInterface $client, RequestInterface $request, UriInterface $uri)
    {
        $this->client = $client;
        $this->request = $request;
        $this->uri = $uri;
    }

    /**
     * Returns currency course data from CBR
     * @param DateTime $dateTime
     * @return CbrDataProviderResult
     * @throws CbrDataInternalException
     */
    public function getCurrencyCoursesOnDate(DateTime $dateTime): CbrDataProviderResult
    {
        $uri = (clone $this->uri)
            ->withScheme(self::CBR_DATA_SCHEME)
            ->withHost(self::CBR_DATA_HOST)
            ->withPath(self::CBR_DATA_PATH)
            ->withQuery('date_req=' . $dateTime->format('d/m/Y'));

        $request = (clone $this->request)->withUri($uri);

        $attempt = 0;
        $response = null;
        while ($attempt < self::ATTEMPTS) {
            $attempt++;
            try {
                $response = $this->client->sendRequest($request);
                if ($response->getStatusCode() !== 200) {
                    continue;
                }
            } catch (ClientExceptionInterface $e) {
                if ($attempt === self::ATTEMPTS) {
                    throw new CbrDataInternalException($e->getMessage(), $e->getCode(), $e);
                }
                continue;
            }
        }

        if ($response->getStatusCode() !== 200) {
            throw new CbrDataInternalException('Non 200 response from CBR: code ' . $response->getStatusCode());
        }

        $bodyContent = $response->getBody()->getContents();
        if (!$bodyContent) {
            throw new CbrDataInternalException('Empty response from CBR');
        }

        $currencyCourses = [];
        $xml = new SimpleXMLElement($bodyContent);

        try {
            $tradeDay = new DateTime((string) $xml->attributes()['Date']);
        } catch (Exception $e) {
            throw new CbrDataInternalException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            foreach ($xml->children() as $currency) {
                $charCode = (string) $currency->CharCode;
                if (!$charCode) {
                    continue;
                }
                $currencyCourses[] = new CurrencyCourse(
                    new CurrencyEnum($charCode),
                    (int) $currency->Nominal,
                    (float) str_replace(',', '.', $currency->Value)
                );
            }
        } catch (UnexpectedValueException $e) {
            throw new CbrDataInternalException($e->getMessage(), $e->getCode(), $e);
        }

        if (empty($currencyCourses)) {
            throw new CbrDataInternalException('Empty currency set from CBR');
        }

        $beforeRoubleCodeChange = $dateTime < (new DateTime('2001-01-01'));

        // Russian rouble since 2001
        $currencyCourses[] = new CurrencyCourse(
            CurrencyEnum::RUB(),
            1,
            $beforeRoubleCodeChange ? 1000 : 1
        );

        // Russian rouble since 1994
        $currencyCourses[] = new CurrencyCourse(
            CurrencyEnum::RUR(),
            1000,
            $beforeRoubleCodeChange ? 1000 : 1
        );

        return new CbrDataProviderResult($currencyCourses, $tradeDay);
    }
}
