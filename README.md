# CBR Currency Course Viewer Backend 

Based on Laravel Lumen.

### Available routes

`/course/{targetCurrency}/{baseCurrency}/{date}` - returns target currency course by base currency on specific date
`/availableCurrencies` - returns available currency ISO codes

### Installation

1. `git clone`
2. `composer install`
3. Install Redis for caching
4. Create `.env` based on `.env.example`

To run:
1. Local server - `php -S localhost:8000 -t public`
2. Nginx - see https://laravel.com/docs/8.x/deployment#nginx
3. Apache - see https://clouding.io/hc/en-us/articles/360013637759-How-To-Setup-Lumen-API-on-Ubuntu

### Tests

Run `phpunit` globally or `./vendor/phpunit/phpunit/phpunit` locally
