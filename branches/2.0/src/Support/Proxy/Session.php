<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Session\Session as SessionContract;
use tiFy\Contracts\Session\Store;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface as FlashBag;

/**
 * @method static FlashBag|SessionContract|mixed flash(string|array|null $key = null, mixed $value = null)
 * @method static Store registerStore(string $name, array $attrs = [])
 */
class Session extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'session';
    }
}