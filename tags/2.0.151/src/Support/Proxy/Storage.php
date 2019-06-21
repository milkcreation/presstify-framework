<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Filesystem\Filesystem;

/**
 * @method static Filesystem|null disk(string $name)
 */
class Storage extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'storage';
    }
}