<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Field\FieldFactory;

/**
 * @method static FieldFactory|null get(string $name, array|string|null $id = null, array $attrs = [])
 */
class Field extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'field';
    }
}