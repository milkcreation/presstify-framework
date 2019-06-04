<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Support\Proxy;

class Field extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'field';
    }
}