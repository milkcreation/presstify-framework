<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Session\Session as SessionContract;
use tiFy\Contracts\Session\Store;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface as FlashBag;

/**
 * @method static FlashBag|SessionContract|mixed flash(string|array|null $key = null, mixed $value = null)
 * @method static Store registerStore(string $name, array|Store|null ...$args)
 * @method static bool start()
 * @method static Store|null store(string $name)
 */
class Session extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|SessionContract
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
        return 'session';
    }
}