<?php declare(strict_types=1);

namespace tiFy\Debug;

use Exception;
use Psr\Container\ContainerInterface as Container;
use Whoops\Run as ErrorHandler;
use Whoops\Handler\PrettyPageHandler as ErrorHandlerRenderer;
use tiFy\Contracts\Debug\Debug as DebugContract;
use tiFy\Contracts\Debug\DebugDriver as DebugDriverContract;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Support\Env;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Storage;

class Debug implements DebugContract
{
    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $booted = false;

    /**
     * Instance du pilote associé.
     * @var DebugDriverContract
     */
    private $driver;

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    protected $config;

    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container|null
     */
    protected $container;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (Env::isDev()) {
            (new ErrorHandler())
                ->pushHandler(new ErrorHandlerRenderer())
                ->register();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): DebugContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new Exception(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): DebugContract
    {
        if (!$this->booted) {
            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if (!isset($this->config) || is_null($this->config)) {
            $this->config = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->config->get($key, $default);
        } elseif (is_array($key)) {
            return $this->config->set($key);
        } else {
            return $this->config;
        }
    }

    /**
     * Instance du pilote associé.
     *
     * @return DebugDriverContract
     */
    public function driver(): DebugDriverContract
    {
        if (is_null($this->driver)) {
            $this->driver = $this->resolvable(DebugDriverContract::class)
                ? $this->resolve(DebugDriverContract::class) : new DebugDriver($this);
        }

        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function getFooter(): string
    {
        return $this->driver()->getFooter();
    }

    /**
     * @inheritDoc
     */
    public function getHead(): string
    {
        return $this->driver()->getHead();
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->driver()->render();
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $alias): ?object
    {
        return ($container = $this->getContainer()) ? $container->get($alias) : null;
    }

    /**
     * @inheritDoc
     */
    public function resolvable(string $alias): bool
    {
        return ($container = $this->getContainer()) && $container->has($alias);
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) ||is_null($this->resources)) {
            $this->resources = Storage::local(__DIR__ . '/Resources');
        }

        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): DebugContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container): DebugContract
    {
        $this->container = $container;

        return $this;
    }
}