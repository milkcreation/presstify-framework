<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\PostType\{PostTypeFactory, PostTypePostMeta, PostTypeStatus};

/**
 * @method static PostTypeFactory|null get(string $name)
 * @method static PostTypePostMeta meta()
 * @method static PostTypeFactory register(string $name, array $args = [])
 * @method static PostTypeStatus status(string $name, array $args = [])
 */
class PostType extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'post-type';
    }
}