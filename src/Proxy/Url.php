<?php

declare(strict_types=1);

namespace tiFy\Proxy;

use League\Uri\Contracts\UriInterface as LeagueUri;
use Pollen\Proxy\AbstractProxy;
use Psr\Http\Message\UriInterface;
use tiFy\Contracts\Routing\Url as UrlContract;
use tiFy\Contracts\Routing\UrlFactory;

/**
 * @method static UrlContract appendSegment(string $segment)
 * @method static UrlFactory current(bool $full = true)
 * @method static UrlContract deleteSegment(string $segment)
 * @method static string decoded(bool $raw = true)
 * @method static LeagueUri|UriInterface get()
 * @method static array params(string|null $key = null, string|null $default = null);
 * @method static string|null rel(string $url)
 * @method static string rewriteBase()
 * @method static UrlFactory root(string $path = '')
 * @method static string scope()
 * @method static UrlContract set(UriInterface|LeagueUri|string $uri)
 * @method static UrlContract with(array $args)
 * @method static UrlContract without(string[] $args)
 */
class Url extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|UrlContract
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return 'url';
    }
}