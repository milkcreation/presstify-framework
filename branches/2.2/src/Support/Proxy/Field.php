<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Closure;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Field\FieldDriverInterface;

/**
 * @method static FieldDriverInterface|null get(string $name, array|string|null $id = null, array $attrs = [])
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static FieldDriverInterface register(string $name, FieldDriverInterface|Closure|string $partial)
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
        return FieldContract::class;
    }
}