<?php declare(strict_types=1);

namespace tiFy\Partial;

use Closure;
use Exception, InvalidArgumentException, RuntimeException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Partial\Accordion;
use tiFy\Contracts\Partial\Breadcrumb;
use tiFy\Contracts\Partial\BurgerButton;
use tiFy\Contracts\Partial\CookieNotice;
use tiFy\Contracts\Partial\CurtainMenu;
use tiFy\Contracts\Partial\Dropdown;
use tiFy\Contracts\Partial\Downloader;
use tiFy\Contracts\Partial\FlashNotice;
use tiFy\Contracts\Partial\Holder;
use tiFy\Contracts\Partial\ImageLightbox;
use tiFy\Contracts\Partial\MenuDriver;
use tiFy\Contracts\Partial\Modal;
use tiFy\Contracts\Partial\Notice;
use tiFy\Contracts\Partial\Pagination;
use tiFy\Contracts\Partial\Partial as PartialManagerContract;
use tiFy\Contracts\Partial\PartialDriver;
use tiFy\Contracts\Partial\Pdfviewer;
use tiFy\Contracts\Partial\Progress;
use tiFy\Contracts\Partial\Sidebar;
use tiFy\Contracts\Partial\Slider;
use tiFy\Contracts\Partial\Spinner;
use tiFy\Contracts\Partial\Tab;
use tiFy\Contracts\Partial\Table;
use tiFy\Contracts\Partial\Tag;
use tiFy\Contracts\Routing\Route;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Storage;

class Partial implements PartialManagerContract
{
    use ContainerAwareTrait, BootableTrait;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Définition des pilotes par défaut.
     * @var array
     */
    private $defaultDrivers = [
        'accordion'      => Accordion::class,
        'breadcrumb'     => Breadcrumb::class,
        'burger-button'  => BurgerButton::class,
        'cookie-notice'  => CookieNotice::class,
        'curtain-menu'   => CurtainMenu::class,
        'dropdown'       => Dropdown::class,
        'downloader'     => Downloader::class,
        'flash-notice'   => FlashNotice::class,
        'holder'         => Holder::class,
        'image-lightbox' => ImageLightbox::class,
        'menu'           => MenuDriver::class,
        'modal'          => Modal::class,
        'notice'         => Notice::class,
        'pagination'     => Pagination::class,
        'pdfviewer'      => Pdfviewer::class,
        'progress'       => Progress::class,
        'sidebar'        => Sidebar::class,
        'slider'         => Slider::class,
        'spinner'        => Spinner::class,
        'tab'            => Tab::class,
        'table'          => Table::class,
        'tag'            => Tag::class
    ];

    /**
     * Liste des instance de pilote chargés.
     * @var PartialDriver[][]
     */
    private $drivers = [];

    /**
     * Liste des pilotes déclarés.
     * @var PartialDriver[][]|Closure[][]|string[][]|array
     */
    protected $driverDefinitions = [];

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Route de traitement des requêtes XHR.
     * @var Route|null
     */
    private $xhrRoute;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    protected $config;

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
    public static function instance(): PartialManagerContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): PartialManagerContract
    {
        if (!$this->isBooted()) {
            $this->xhrRoute = Router::xhr(
                md5('PartialManager') . '/{partial}/{controller}',
                [$this, 'xhrResponseDispatcher']
            );

            $this->registerDefaultDrivers();

            $this->setBooted();
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
     * @inheritDoc
     */
    public function get(string $alias, $idOrParams = null, array $params = []): ?PartialDriver
    {
        if (is_array($idOrParams)) {
            $params = $idOrParams;
            $id = null;
        } else {
            $id = $idOrParams;
        }

        if ($id && isset($this->drivers[$alias][$id])) {
            return $this->drivers[$alias][$id];
        } elseif (!$driver = $this->getDriverFromDefinition($alias)) {
            return null;
        }

        $this->drivers[$alias] = $this->drivers[$alias] ?? [];
        $index = count($this->drivers[$alias]);
        $id = $id ?? $alias . $index;
        if (!$driver->getAlias()) {
            $driver->setAlias($alias);
        }
        $params = array_merge($driver->defaultParams(), $this->config("driver.{$alias}", []), $params);

        $driver->setIndex($index)->setId($id)->setParams($params)->boot();

        return $this->drivers[$alias][$id] = $driver;
    }

    /**
     * Récupération d'une instance de pilote depuis une définition.
     *
     * @param string $alias
     *
     * @return PartialDriver|null
     */
    protected function getDriverFromDefinition(string $alias): ?PartialDriver
    {
        if (!$def = $this->driverDefinitions[$alias] ?? null) {
            throw new InvalidArgumentException(sprintf('Partial with alias [%s] unavailable', $alias));
        }

        if ($def instanceof PartialDriver) {
            return clone $def;
        } elseif (is_string($def) && $this->containerHas($def)) {
            if ($this->containerHas($def)) {
                return $this->containerGet($def);
            }
        } elseif(is_string($def) && class_exists($def)) {
            return new $def($this);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getXhrRouteUrl(string $partial, ?string $controller = null, array $params = []): string
    {
        $controller = $controller ?? 'xhrResponse';

        return $this->xhrRoute->getUrl(array_merge($params, compact('partial', 'controller')));
    }

    /**
     * @inheritDoc
     */
    public function register(string $alias, $driverDefinition, ?Closure $callback = null): PartialManagerContract
    {
        if (isset($this->driverDefinitions[$alias])) {
            throw new RuntimeException(sprintf('Another PartialDriver with alias [%s] already registered', $alias));
        }

        $this->driverDefinitions[$alias] = $driverDefinition;

        if ($callback !== null) {
            $callback($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerDefaultDrivers(): PartialManagerContract
    {
        foreach ($this->defaultDrivers as $name => $alias) {
            $this->register($name, $alias);
        }

        return $this;
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
    public function setConfig(array $attrs): PartialManagerContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseDispatcher(string $partial, string $controller, ...$args): array
    {
        try {
            $driver = $this->get($partial);
        } catch(Exception $e) {
            throw new NotFoundException(
                sprintf('PartialDriver [%s] return exception : %s', $partial, $e->getMessage())
            );
        }

        try {
            return $driver->{$controller}(...$args);
        } catch(Exception $e) {
            throw new NotFoundException(
                sprintf('PartialDriver [%s] Controller [%s] call return exception', $controller, $partial)
            );
        }
    }
}