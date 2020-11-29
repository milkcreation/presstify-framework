<?php declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use tiFy\Support\Proxy\AbstractProxy;

/**
 * @method static string|null getBase64Src(int $id)
 * @method static string|null getSrcFilename(string $src)
 */
class Media extends AbstractProxy
{
    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return 'wp.media';
    }
}