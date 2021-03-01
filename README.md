# laravel-torblock
[![Latest Stable Version](https://poser.pugx.org/taavi/laravel-torblock/v)](//packagist.org/packages/taavi/laravel-torblock)
[![Total Downloads](https://poser.pugx.org/taavi/laravel-torblock/downloads)](//packagist.org/packages/taavi/laravel-torblock)
[![License](https://poser.pugx.org/taavi/laravel-torblock/license)](//packagist.org/packages/taavi/laravel-torblock)

Block access to use your Laravel application via Tor to prevent abuse.

## Usage

### Getting started

First, install the package:

```
composer require taavi/laravel-torblock
```

Then, the simplest way to use this package is to simply add the middleware to the HTTP kernel:

```php
class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        'torblock' => \Taavi\LaravelTorblock\Middleware\BlockTorAccess::class,        
    ];
}
```

After registering the middleware you can just add the `torblock` middleware to any route that you want to prevent Tor
access on.

### Advanced usage

Internally the middleware just throws a `TorBlocked` exception to prevent access. Laravel's exception handler, by
default, displays that as a 403 page and does not log it to the exception log. You can use the exception handler to
customize how the user sees that error. You can also use the `vendor:publish` artisan command to publish the language
files used to generate the exception message.

Tor exit node data is cached by default for one day. Use the `vendor:publish` artisan command to publish a configuration
file to customize the caching duration or the Onionoo server used to load the data from.

### Testing

To improve test performance, you can swap the package to use a fake implementation of the `TorExitNodeService`:

```php
namespace Tests;

use Taavi\LaravelTorblock\Service\FakeTorExitNodeService;
use Taavi\LaravelTorblock\Service\TorExitNodeService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(TorExitNodeService::class, FakeTorExitNodeService::class);
    }
}
```

The fake implementation uses two RFC 5737 TEST-NET-1 addresses as the list of blocked addresses. 

## Credits

This package is based on the [TorBlock](https://mediawiki.org/wiki/Extension:TorBlock) MediaWiki extension which is
licensed GPL-2.0 or later.
