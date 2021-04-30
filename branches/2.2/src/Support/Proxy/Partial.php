<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Closure;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\PartialDriverInterface;

/**
 * @method static PartialDriverInterface|null get(string $alias, array|string|null $idOrParams = null, array $params = [])
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static PartialContract register(string $alias, PartialDriverInterface|Closure|string $driverDefinition)
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
        return PartialContract::class;
    }
}