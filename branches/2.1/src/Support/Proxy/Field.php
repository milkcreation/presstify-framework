<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Field\Field as FieldContract;
use tiFy\Contracts\Field\FieldDriver;

/**
 * @method static FieldDriver|null get(string $name, array|string|null $id = null, array $attrs = [])
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static FieldDriver register(string $name, FieldDriver $partial)
 */
class Field extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|FieldContract
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
        return 'field';
    }
}