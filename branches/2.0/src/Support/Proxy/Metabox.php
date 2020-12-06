<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Metabox\Metabox as MetaboxContract;
use tiFy\Contracts\Metabox\MetaboxContext as Context;
use tiFy\Contracts\Metabox\MetaboxDriver as Driver;
use tiFy\Contracts\Metabox\MetaboxScreen as Screen;

/**
 * @method static Driver add(string $alias, string|array|Driver|null $driverDef, string|null $screen = null, string|null $context = null)
 * @method static Screen addContext(string $alias, string|array|Context $contextDef)
 * @method static Screen addScreen(string $alias, string|array|Screen $screenDef)
 * @method static mixed config(string|array|null $key = null, $default = null)
 * @method static Driver|null get(string $alias)
 * @method static Context|null getContext(string $alias)
 * @method static Screen|null getScreen(string $alias)
 * @method static MetaboxContract registerContext(string $alias, Context $context)
 * @method static MetaboxContract registerDriver(string $alias, Driver $driver)
 * @method static MetaboxContract registerScreen(string $alias, Screen $screen)
 * @method static string render(string $context)
 * @method static MetaboxContract stack(string|Screen $screen, string|Context $context, Driver[]|array[]|string[] $driversDef = [])
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
        return 'metabox';
    }
}