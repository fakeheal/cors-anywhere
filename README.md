# cors-anywhere
[![Tests](https://github.com/fakeheal/cors-anywhere/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/fakeheal/cors-anywhere/actions/workflows/php.yml) 
[![Latest Stable Version](http://poser.pugx.org/fakeheal/cors-anywhere/v)](https://packagist.org/packages/fakeheal/cors-anywhere) 
[![Total Downloads](http://poser.pugx.org/fakeheal/cors-anywhere/downloads)](https://packagist.org/packages/fakeheal/cors-anywhere) 
[![License](http://poser.pugx.org/fakeheal/cors-anywhere/license)](https://packagist.org/packages/fakeheal/cors-anywhere) 
[![PHP Version Require](http://poser.pugx.org/fakeheal/cors-anywhere/require/php)](https://packagist.org/packages/fakeheal/cors-anywhere)

`cors-anywhere` is a php reverse proxy which adds CORS headers to the proxy request.

## Use Case

It can be used to access resources from third party websites when it's not possible to enable CORS on target website
i.e. when you don't own that website.

_Note from author:_ I am currently using to access **Rescue Time**'s & **trak.tv** APIs, so I can sync my own data to [conjure.so](https://conjure.so).

## Documentation

### Install

```sh
composer require fakeheal/cors-anywhere
```

### Initialize

```php
<?php

use Fakeheal\CorsAnywhere\Exceptions\CorsAnywhereException;
use Fakeheal\CorsAnywhere\Proxy;

// ...

try {
    $server = new Proxy([
        // allowed hosts to proxy to
        'rescuetime.com',
        'google.com'
    ], [
        // allowed headers
        'Content-Type',
        'Accepts'    
    ]);

    // call handle that... handles everything
    $server->handle();
} catch (CorsAnywhereException $e) {
    die($e->getMessage()); // or die trying
}
```
### How to use 
Once setup, you must pass a url parameter, for example if your Cors URL is the following: `http://127.0.0.1:2000`
Then you'd do the below, the url being the proxied site you want to use.

```
http://127.0.0.1:2000/?url=https://google.com
```

## Acknowledgements

- [cors-anywhere, but for node js](https://github.com/Rob--W/cors-anywhere)
- [softius/php-cross-domain-proxy](https://github.com/softius/php-cross-domain-proxy)


## Running Tests

To run tests, run the following command

```bash
  ./vendor/bin/pest
```

## Used By

This project is used by the following entities:

- [Conjure Kit](conjure-kit.vercel.app)

## License

[MIT](https://choosealicense.com/licenses/mit/)

