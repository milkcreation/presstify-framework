<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Partial\PartialFactory;

/**
 * @method static PartialFactory|null get(string $name, array|string|null $id = null, array $attrs = [])
 */
class Partial extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'partial';
    }
}