<?php

use Fakeheal\CorsAnywhere\Exceptions\HostNotAllowedException;
use Fakeheal\CorsAnywhere\Proxy;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

it('throws exception if disallowed host is passed', function () {
    $request = new Request(['url' => 'https://google.com']);

    (new Proxy(['https://definitely-not-google.com'], $request))
        ->handle();
})->throws(HostNotAllowedException::class);

it('passes headers up from proxy request response', function () {
    $mock = new MockHandler([
        new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
    ]);
    $client = new Client(['handler' => HandlerStack::create($mock)]);
    $request = new Request(['url' => 'https://google.com']);
    $response = new HttpFoundationResponse();

    (new Proxy(['google.com'], $request, $response, $client))
        ->handle();

    expect($response->headers->get('X-Foo'))->toBe('Bar');
});

it('passes headers down to proxy request', function () {
    // History middleware for "recording" requests made during test
    // https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
    $container = [];
    $history = Middleware::history($container);
    $mock = new MockHandler([
        new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
    ]);
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($history);
    $client = new Client(['handler' => $handlerStack]);

    // Add custom header to proxy request
    $request = new Request(['url' => 'https://google.com'], [], [], [], [], ['HTTP_X-Foo' => 'Bar']);
    $response = new HttpFoundationResponse();

    (new Proxy(['google.com'], $request, $response, $client))
        ->handle();

    expect(count($container))->toBe(1)
        ->and($container[0]['request']->getMethod())->toBe('GET')
        ->and($container[0]['request']->getHeader('X-Foo')[0])->toBe('Bar');
});