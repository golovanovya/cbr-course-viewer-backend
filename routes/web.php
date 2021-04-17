<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/course/{targetCurrency}/{baseCurrency}/{date}', 'CourseController@getCourse');
$router->get('/availableCurrencies', 'CourseController@getAvailableCurrencies');
