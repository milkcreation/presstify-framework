<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use DateTimeInterface;
use Pollen\Cookie\CookieInterface;
use Pollen\Cookie\CookieJarInterface;
use Symfony\Component\HttpFoundation\Cookie as BaseCookie;

/**
 * @method static CookieJarInterface add(CookieInterface $cookie)
 * @method static CookieInterface[]|BaseCookie[]|array all()
 * @method static CookieInterface[]|BaseCookie[]|array fetchQueued()
 * @method static CookieInterface|BaseCookie|null get(string $alias)
 * @method static int getAvailability($lifetime = null)
 * @method static string|null getSalt()
 * @method static CookieInterface|BaseCookie make(string $alias, string|array|null $attrs = null)
 * @method static CookieJarInterface setLifetime(int|string|DateTimeInterface $lifetime)
 * @method static CookieJarInterface setSalt(string $salt)
 */
class Cookie extends AbstractProxy
{
    /**
     * @inheritDoc
     *
     * @return CookieJarInterface
     */
    public static function getInstance(): CookieJarInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return CookieJarInterface::class;
    }
}