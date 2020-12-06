<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Partial\Partial as PartialContract;
use tiFy\Contracts\Partial\PartialDriver;

/**
 * @method static PartialDriver|null get(string $name, array|string|null $id = null, array $attrs = [])
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static PartialContract register(string $name, PartialDriver $partial)
 */
class Partial extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|PartialContract
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
        return 'partial';
    }
}