<?php declare(strict_types=1);

namespace tiFy\Metabox;

use Exception;
use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Metabox\MetaboxContext;
use tiFy\Contracts\Metabox\MetaboxDriver;
use tiFy\Contracts\Metabox\Metabox as MetaboxContract;
use tiFy\Contracts\Metabox\MetaboxScreen;
use tiFy\Contracts\Metabox\ColorDriver;
use tiFy\Contracts\Metabox\CustomHeaderDriver;
use tiFy\Contracts\Metabox\ExcerptDriver;
use tiFy\Contracts\Metabox\FilefeedDriver;
use tiFy\Contracts\Metabox\IconDriver;
use tiFy\Contracts\Metabox\ImagefeedDriver;
use tiFy\Contracts\Metabox\OrderDriver;
use tiFy\Contracts\Metabox\PostfeedDriver;
use tiFy\Contracts\Metabox\RelatedTermDriver;
use tiFy\Contracts\Metabox\SlidefeedDriver;
use tiFy\Contracts\Metabox\SubtitleDriver;
use tiFy\Contracts\Metabox\TabContext;
use tiFy\Contracts\Metabox\VideofeedDriver;
use tiFy\Support\ClassInfo;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Storage;

class Metabox implements MetaboxContract
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
     * Liste des contextes assignés.
     * @var MetaboxContext[]
     */
    private $assignedContexts = [];

    /**
     * Liste des pilotes assignés.
     * @var MetaboxDriver[]
     */
    private $assignedDrivers = [];

    /**
     * Liste des écrans assignés.
     * @var MetaboxScreen[]
     */
    private $assignedScreens = [];

    /**
     * Définition des pilotes par défaut.
     * @var array
     */
    private $defaultDrivers = [
        'color'         => ColorDriver::class,
        'custom-header' => CustomHeaderDriver::class,
        'excerpt'       => ExcerptDriver::class,
        'filefeed'      => FilefeedDriver::class,
        'icon'          => IconDriver::class,
        'imagefeed'     => ImagefeedDriver::class,
        'order'         => OrderDriver::class,
        'postfeed'      => PostfeedDriver::class,
        'related-term'  => RelatedTermDriver::class,
        'slidefeed'     => SlidefeedDriver::class,
        'subtitle'      => SubtitleDriver::class,
        'videofeed'     => VideofeedDriver::class,
    ];

    /**
     * Définition des contextes par défaut.
     * @var array
     */
    private $defaultContexts = [
        'tab' => TabContext::class,
    ];

    /**
     * Définition des écrans par défaut.
     * @var array
     */
    private $defaultScreens = [];

    /**
     * Liste des contextes déclarés.
     * @var MetaboxContext[]
     */
    private $registeredContexts = [];

    /**
     * Liste des pilotes déclarées.
     * @var MetaboxDriver[]
     */
    private $registeredDrivers = [];

    /**
     * Liste des écrans déclarées.
     * @var MetaboxScreen[]
     */
    private $registeredScreens = [];

    /**
     * Liste des instances de pilotes affichés selon leur contexte d'affichage.
     * @var MetaboxDriver[][]
     */
    private $renderedDrivers = [];

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

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): MetaboxContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new Exception(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function add(string $alias, $driver = null, ?string $screen = null, ?string $context = null): MetaboxDriver
    {
        $attrs = [];

        if ($this->get($alias)) {
            throw new Exception(sprintf('Metabox Driver with alias [%s] already assigned', $alias));
        }

        if (is_array($driver)) {
            $attrs = $driver;

            $driver = $attrs['driver'] ?? '';
            unset($attrs['driver']);
        }

        if (!$driver) {
            $driver = $this->resolve('driver');

            try {
                $this->registerDriver(md5($alias . json_encode($attrs)), $driver);
            } catch (Exception $e) {
                throw $e;
            }
        } elseif (is_string($driver) && class_exists($driver)) {
            $alias = $driver;
            $driver = new $driver();

            try {
                $this->registerDriver($alias, $driver);
            } catch (Exception $e) {
                throw $e;
            }
        } elseif (is_string($driver)) {
            $exists = $this->getRegisteredDriver($driver);
            $driver = clone $exists;
        } elseif (is_object($driver) && $driver instanceof MetaboxDriver) {
            if (!$alias = $driver->getAlias()) {
                $alias = (new ClassInfo($driver))->getKebabName();
            }

            if (!$this->getRegisteredDriver($alias)) {
                try {
                    $this->registerDriver($alias, $driver);
                } catch (Exception $e) {
                    throw $e;
                }
            }
        }

        if (!is_object($driver) || !($driver instanceof MetaboxDriver)) {
            throw new Exception(sprintf('Unable to define Metabox driver with alias [%s]', $alias));
        }

        $driver = $driver->set($attrs)->boot();

        if ($screen) {
            $driver->setScreen($screen);
        }

        if ($context) {
            $driver->setContext($context);
        }

        return $this->assignedDrivers[$alias] = $driver;
    }

    /**
     * @inheritDoc
     */
    public function addContext(string $alias, $context = null): MetaboxContext
    {
        if ($exists = $this->getContext($alias)) {
            return $exists;
        }

        $attrs = [];
        if (is_array($context)) {
            $attrs = $context;
            $context = null;
        }

        try {
            if (!$context) {
                $context = $this->resolve('context');
                $this->registerContext($alias, $context);
            } elseif (is_string($context) && class_exists($context)) {
                $alias = $context;
                $context = new $context();

                $this->registerContext($alias, $context);

            } elseif (is_string($context)) {
                $context = $this->getRegisteredContext($context);
            } elseif (is_object($context) && $context instanceof MetaboxContext) {
                $context->setAlias($alias);

                if (!$this->getRegisteredContext($alias)) {
                    $this->registerContext($alias, $context);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        if (!is_object($context) || !($context instanceof MetaboxContext)) {
            throw new Exception(sprintf('Unable to define Metabox context with alias [%s]', $alias));
        }

        return $this->assignedContexts[$alias] = $context->set($attrs)->boot();
    }

    /**
     * @inheritDoc
     */
    public function addScreen(string $alias, $screen = null): MetaboxScreen
    {
        if ($exists = $this->getScreen($alias)) {
            return $exists;
        }

        $attrs = [];
        if (is_array($screen)) {
            $attrs = $screen;
            $screen = null;
        }

        if (!$screen) {
            $screen = $this->resolve('screen');

            try {
                $this->registerScreen($alias, $screen);
            } catch (Exception $e) {
                throw $e;
            }
        } elseif (is_string($screen) && class_exists($screen)) {
            $alias = $screen;
            $screen = new $screen();

            try {
                $this->registerScreen($alias, $screen);
            } catch (Exception $e) {
                throw $e;
            }
        } elseif (is_string($screen)) {
            $screen = $this->getRegisteredScreen($screen);
        } elseif (is_object($screen) && $screen instanceof MetaboxScreen) {
            $screen->setAlias($alias);

            if (!$this->getRegisteredScreen($alias)) {
                try {
                    $this->registerScreen($alias, $screen);
                } catch (Exception $e) {
                    throw $e;
                }
            }
        }

        if (!is_object($screen) || !($screen instanceof MetaboxScreen)) {
            throw new Exception(sprintf('Unable to define Metabox screen with alias [%s]', $alias));
        }

        return $this->assignedScreens[$alias] = $screen->set($attrs)->boot();
    }


    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->assignedDrivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxContract
    {
        if (!$this->booted) {
            try {
                foreach ($this->defaultDrivers as $alias => $abstract) {
                    if ($this->getContainer()->has($abstract)) {
                        $this->registerDriver($alias, $this->getContainer()->get($abstract));
                    }
                }

                foreach ($this->defaultContexts as $alias => $abstract) {
                    if ($this->getContainer()->has($abstract)) {
                        $this->registerContext($alias, $this->getContainer()->get($abstract));
                    }
                }

                foreach ($this->defaultScreens as $alias => $abstract) {
                    if ($this->getContainer()->has($abstract)) {
                        $this->registerScreen($alias, $this->getContainer()->get($abstract));
                    }
                }
            } catch (Exception $e) {
                throw $e;
            }

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchRender(MetaboxContext $context, ?MetaboxScreen $screen = null): array
    {
        if (is_null($screen)) {
            $screens = (new Collection($this->assignedScreens))->filter(function (MetaboxScreen $screen) {
                return $screen->isCurrent();
            })->all();
        } else {
            $screens = [$screen];
        }

        return (new Collection($this->all()))->filter(function (MetaboxDriver $driver) use ($context, $screens) {
            return ($driver->getContext() === $context) && in_array($driver->getScreen(), array_values($screens));
        })->all();
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
     * @inheritDoc
     */
    public function get(string $alias): ?MetaboxDriver
    {
        return $this->assignedDrivers[$alias] ?? null;
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
    public function getContext(string $alias): ?MetaboxContext
    {
        return $this->assignedContexts[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getRegisteredContext(string $alias): ?MetaboxContext
    {
        return $this->registeredContexts[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getRegisteredDriver(string $alias): ?MetaboxDriver
    {
        return $this->registeredDrivers[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getRegisteredScreen(string $alias): ?MetaboxScreen
    {
        return $this->registeredScreens[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getRenderedDrivers(string $context): array
    {
        return $this->renderedDrivers[$context] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getScreen(string $alias): ?MetaboxScreen
    {
        return $this->assignedScreens[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function registerContext(string $alias, MetaboxContext $context): MetaboxContract
    {
        if (isset($this->registeredDrivers[$alias])) {
            throw new Exception(sprintf('Metabox context with alias [%s] already registered', $alias));
        } else {
            $this->registeredContexts[$alias] = $context->setMetabox($this)->setAlias($alias)->build();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerDriver(string $alias, MetaboxDriver $driver): MetaboxContract
    {
        if (isset($this->registeredDrivers[$alias])) {
            throw new Exception(sprintf('Metabox driver with alias [%s] already registered', $alias));
        } else {
            $this->registeredDrivers[$alias] = $driver->setMetabox($this)->setAlias($alias)->build();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerScreen(string $alias, MetaboxScreen $screen): MetaboxContract
    {
        if (isset($this->registeredScreens[$alias])) {
            throw new Exception(sprintf('Metabox screen with alias [%s] already registered', $alias));
        } else {
            $this->registeredScreens[$alias] = $screen->setMetabox($this)->setAlias($alias)->build();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(string $context, $args = []): string
    {
        if ($ctx = $this->getContext($context)) {
            if ($this->renderedDrivers[$context] = $items = $this->fetchRender($ctx)) {
                foreach ($items as $item) {
                    $item->handle($args);
                }
            }

            return $ctx->render();
        } else {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $alias)
    {
        return ($container = $this->getContainer()) ? $container->get("metabox.{$alias}") : null;
    }

    /**
     * @inheritDoc
     */
    public function resolvable(string $alias): bool
    {
        return ($container = $this->getContainer()) && $container->has("metabox.{$alias}");
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(__DIR__ . '/Resources');
        }

        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): MetaboxContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container): MetaboxContract
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stack(string $screen, string $context, array $driversDef): MetaboxContract
    {
        foreach ($driversDef as $alias => $driverDef) {
            try {
                $this->add($alias, $driverDef, $screen, $context);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return $this;
    }
}