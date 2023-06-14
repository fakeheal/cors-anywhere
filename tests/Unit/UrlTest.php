<?php

use Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException;
use Fakeheal\CorsAnywhere\Url;

it('throws exception when url is an empty string', function() {
    new Url('');
})->throws(NoValidUrlProvidedException::class);

it('throws exception when url is missing schema', function() {
    new Url('example.com');
})->throws(NoValidUrlProvidedException::class);

it('throws exception when url is missing host', function() {
    new Url('https://');
})->throws(NoValidUrlProvidedException::class);

it('throws exception when url cannot be parsed', function() {
    new Url('https:/ee.com');
})->throws(NoValidUrlProvidedException::class);

it('url parses strings properly', function() {
   $url = new Url('http://google.com');

   expect($url->getHost())->toBe('google.com')
       ->and($url->getScheme())->toBe('http')
       ->and($url->getPlain())->toBe('http://google.com');
});
it('url handles subdomains properly', function() {
   $url = new Url('http://www.google.com');

   expect($url->getHost())->toBe('www.google.com')
       ->and($url->getScheme())->toBe('http')
       ->and($url->getPlain())->toBe('http://www.google.com');
});