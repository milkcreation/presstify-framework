<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Closure;
use Pollen\Field\FieldDriverInterface;
use Pollen\Field\FieldManagerInterface;

/**
 * @method static FieldDriverInterface[] all()
 * @method static FieldDriverInterface|null get(string $alias, $idOrParams = null, array|null $params = [])
 * @method static string|null getXhrRouteUrl(string $field, ?string $controller = null, array $params = [])
 * @method static FieldManagerInterface register(string $alias, $driverDefinition, Closure|null $registerCallback = null)
 */
class Field extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return FieldManagerInterface
     */
    public static function getInstance(): FieldManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return FieldManagerInterface::class;
    }
}