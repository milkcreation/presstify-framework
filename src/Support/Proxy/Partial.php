<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Closure;
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Contracts\Partial\PartialDriver;

/**
 * @method static PartialDriver|null get(string $alias, array|string|null $idOrParams = null, array $params = [])
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static PartialManager register(string $alias, PartialDriver|Closure|string| $driverDefinition)
 */
class Partial extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|PartialManager
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
        return PartialManager::class;
    }
}