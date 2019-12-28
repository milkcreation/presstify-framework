<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\View\{Engine, PlatesEngine, View as ViewContract, PlatesFactory};

/**
 * @method static Engine getEngine(string $name = 'default')
 * @method static PlatesEngine getPlatesEngine()
 * @method static PlatesFactory make(string $view, array $args = [])
 * @method static Engine register(string $name, string|array|Engine|null $attrs = null)
 * @method static string render(string $view, array $args = [])
 * @method static ViewContract setEngine(string $name, Engine $engine)
 */
class View extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return ViewContract
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier()
    {
        return 'view';
    }
}