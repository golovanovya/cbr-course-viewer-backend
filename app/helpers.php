<?php

use App\Services\CbrData\CbrDataService;
use App\Services\CbrData\Facades\CbrData;

/**
 * @return CbrDataService
 */
function cbr()
{
    return CbrData::getFacadeRoot();
}
