# cors-anywhere

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

use Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException;
use Fakeheal\CorsAnywhere\Proxy;

// ...

// allowed hosts passed down to the Proxy class
try {
    $server = new Proxy([
        'rescuetime.com',
        'google.com'
    ]);

    // call handle that... handles everything
    $server->handle();
} catch (NoValidUrlProvidedException $e) {
    die($e->getMessage()); // or die trying
}
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

