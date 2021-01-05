<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Metabox\MetaboxContextInterface;
use tiFy\Metabox\MetaboxDriverInterface;
use tiFy\Metabox\MetaboxScreenInterface;

/**
 * @method static MetaboxDriverInterface add(string $alias, string|array|MetaboxDriverInterface|null $driverDefinition = null, string|null $screen = null, string|null $context = null)
 * @method static MetaboxContextInterface addContext(string $alias, string|array|MetaboxContextInterface $contextDefinition)
 * @method static MetaboxDriverInterface addDriver(string $alias, string|array|MetaboxDriverInterface|null $driverDefinition = null, string|null $screen = null, string|null $context = null)
 * @method static MetaboxScreenInterface addScreen(string $alias, string|array|MetaboxScreenInterface $screenDefinition)
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static MetaboxDriverInterface|null get(string $alias)
 * @method static MetaboxContextInterface|null getContext(string $alias)
 * @method static MetaboxDriverInterface|null getDriver(string $alias)
 * @method static MetaboxScreenInterface|null getScreen(string $alias)
 * @method static MetaboxContract register(string $alias, string|array|MetaboxDriverInterface $driverDefinition)
 * @method static MetaboxContract registerContext(string $alias, string|array|MetaboxContextInterface $contextDefinition)
 * @method static MetaboxContract registerDriver(string $alias, string|array|MetaboxDriverInterface $driverDefinition)
 * @method static MetaboxContract registerScreen(string $alias, string|array|MetaboxScreenInterface $screenDefinition)
 * @method static string render(string $context)
 * @method static MetaboxContract stack(string $screen, string $context, string[][]|array[][]|MetaboxDriverInterface[][] $driversDefinitions)
 */
class Metabox extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|MetaboxContract
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
        return MetaboxContract::class;
    }
}