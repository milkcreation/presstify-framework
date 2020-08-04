<?php declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use tiFy\Support\Proxy\AbstractProxy;

/**
 * @method static
 */
class Upload extends AbstractProxy
{
    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier()
    {
        return 'wp.upload';
    }
}