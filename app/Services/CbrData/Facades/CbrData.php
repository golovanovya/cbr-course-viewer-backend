<?php
declare(strict_types=1);

namespace App\Services\CbrData\Facades;

use App\Services\CbrData\CbrDataService;
use Illuminate\Support\Facades\Facade;

class CbrData extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CbrDataService::class;
    }
}
