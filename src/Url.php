<?php

namespace Fakeheal\CorsAnywhere;

use Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException;

readonly class Url
{
    /**
     * @var string
     */
    private string $plain;

    /**
     * @var string e.g. http
     */
    private string $scheme;

    /**
     * @var string e.g. example.com
     */
    private string $host;

    /**
     * @param string $url url to parse
     * @throws \Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException
     */
    public function __construct(string $url)
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new NoValidUrlProvidedException(sprintf("'%s' is invalid URL.", $url));
        }

        ['scheme' => $scheme, 'host' => $host,] = parse_url($url);

        if (! $host || ! $scheme) {
            throw new NoValidUrlProvidedException(
                sprintf(
                    "'%s' is missing scheme (e.g. http(s) or host (e.g. example.com).",
                    $url
                )
            );
        }

        $this->plain = $url;
        $this->scheme = $scheme;
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPlain(): string
    {
        return $this->plain;
    }
}