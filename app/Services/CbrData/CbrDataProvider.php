<?php

namespace App\Services\CbrData;

use Psr\Http\Message\RequestInterface;
use DateTime;

/**
 * Class CbrDataProvider
 * @package App\Services\CbrData
 */
class CbrDataProvider
{
    private const CBR_DATA_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        
        $this->request = $request;
    }

    public function getCourseOnDate(DateTime $dateTime)
    {

    }
}
