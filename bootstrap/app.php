<?php

use Cache\Adapter\Redis\RedisCachePool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\SimpleCache\CacheInterface;

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    CacheInterface::class,
    function () {
        $client = new Redis();
        $client->connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', 6379));
        return new RedisCachePool($client);
    }
);

$app->singleton(
    ClientInterface::class,
    Client::class
);

$app->singleton(
    RequestInterface::class,
    function () {
        return new Request('GET', '');
    }
);

$app->singleton(
    UriInterface::class,
    Uri::class
);

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
