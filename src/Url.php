<?php

namespace Fakeheal\CorsAnywhere;

use Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException;

readonly class Url
{
    /**
     * @var string e.g. http
     */
    private string $scheme;

    /**
     * @var string e.g. example.com
     */
    private string $host;

    /**
     * @var string|null - e.g. /my-feed
     */
    private ?string $path;

    /**
     * @param string $url url to parse
     * @throws \Fakeheal\CorsAnywhere\Exceptions\NoValidUrlProvidedException
     */
    public function __construct(string $url)
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new NoValidUrlProvidedException(sprintf("'%s' is invalid URL.", $url));
        }

        $parsedUrl = parse_url($url);

        if (! isset($parsedUrl['scheme']) || ! $parsedUrl['scheme'] || ! isset($parsedUrl['host']) || ! $parsedUrl['host'] ) {
            throw new NoValidUrlProvidedException(
                sprintf(
                    "'%s' is missing scheme (e.g. http(s) or host (e.g. example.com).",
                    $url
                )
            );
        }

        $this->scheme = $parsedUrl['scheme'];
        $this->host = $parsedUrl['host'];
        $this->path = $parsedUrl['path'] ?? null;
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
     * @return ?string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Builds URL as string using parsed schema, host & path.
     *
     * @return string
     */
    public function build(): string
    {
        return sprintf("%s://%s%s", $this->getScheme(), $this->getHost(), $this->getPath() ?? '');
    }
}