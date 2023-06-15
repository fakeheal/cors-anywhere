<?php

namespace Fakeheal\CorsAnywhere;

use Fakeheal\CorsAnywhere\Exceptions\HostNotAllowedException;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Proxy
{
    private const METHOD_GET = 'GET';
    private const PARAM_URL = 'url';

    /**
     * URL we are proxying.
     *
     * @var \Fakeheal\CorsAnywhere\Url
     */
    private readonly Url $url;

    /**
     * @param array $allowedHosts Allowed domains for proxying.
     * @param \Symfony\Component\HttpFoundation\Request|null $request Parsed request to the server.
     * @param \Symfony\Component\HttpFoundation\Response|null $response Response of the proxy request.
     *
     * @throws \Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException
     */
    public function __construct(
        private readonly array $allowedHosts,
        private ?Request $request = null,
        private ?Response $response = null,
        private ?Client $client = null
    ) {
        if (is_null($this->request)) {
            $this->request = new Request($_GET, $_POST, [], [], [], $_SERVER);
        }

        if (is_null($this->response)) {
            $this->response = new Response();
        }

        if (is_null($this->client)) {
            $this->client = new Client();
        }

        $this->url = new Url($this->request->get(self::PARAM_URL, ''));
    }

    /**
     * @return \Fakeheal\CorsAnywhere\Proxy
     * @throws \Fakeheal\CorsAnywhere\Exceptions\HostNotAllowedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function redirect(): Proxy
    {
        // are we even allowed to?
        if (! in_array($this->url->getHost(), $this->allowedHosts)) {
            throw new HostNotAllowedException(sprintf("%s is not allowed host.", $this->url->getHost()));
        }

        // make the proxy request
        $proxyResponse = $this->client->request(
            $this->request->getMethod(),
            $this->url->build(),
            array_merge([
                // response to proxy through 404, 500, etc
                'http_errors' => false,
                // "redirect" all headers
                'headers' => $this->request->headers->all(),
            ], $this->buildParameters())
        );

        // pass response headers from proxy request to our response
        foreach ($proxyResponse->getHeaders() as $key => $value) {
            $this->response->headers->set($key, $value);
        }

        // pass response content from proxy request to our response
        $this->response->setContent($proxyResponse->getBody()->getContents());

        return $this;
    }


    /**
     * Sends the prepared response.
     *
     * @return void
     * @throws \Fakeheal\CorsAnywhere\Exceptions\HostNotAllowedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        $this->redirect()->response->send();
    }

    /**
     * Proxy request over GET or not.
     *
     * @return bool
     */
    private function isGetRequest(): bool
    {
        return $this->request->getMethod() === self::METHOD_GET;
    }

    /**
     * Build parameters passed in query string (GET) or body (anything else) for Guzzle's request.
     *
     * @return array
     */
    private function buildParameters(): array
    {
        if ($this->isGetRequest()) {
            return [
                'query' => array_filter(
                    $this->request->query->all(),
                    fn(string $key) => $key !== self::PARAM_URL,
                    ARRAY_FILTER_USE_KEY
                )
            ];
        }

        return [
            'form_params' => $this->request->getPayload()->all()
        ];
    }
}