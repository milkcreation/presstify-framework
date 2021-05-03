<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Closure;
use Pollen\Partial\PartialDriverInterface;
use Pollen\Partial\PartialManagerInterface;

/**
 * @method static PartialDriverInterface[] all()
 * @method static PartialDriverInterface|null get(string $alias, $idOrParams = null, array|null $params = [])
 * @method static string|null getXhrRouteUrl(string $partial, ?string $controller = null, array $params = [])
 * @method static PartialManagerInterface register(string $alias, $driverDefinition, Closure|null $registerCallback = null)
 */
class Partial extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return PartialManagerInterface
     */
    public static function getInstance(): PartialManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return PartialManagerInterface::class;
    }
}