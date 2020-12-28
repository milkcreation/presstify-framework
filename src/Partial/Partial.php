<?php

declare(strict_types=1);

namespace tiFy\Partial;

use Closure;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\Drivers\AccordionDriver;
use tiFy\Partial\Drivers\BreadcrumbDriver;
use tiFy\Partial\Drivers\BurgerButtonDriver;
use tiFy\Partial\Drivers\CookieNoticeDriver;
use tiFy\Partial\Drivers\CurtainMenuDriver;
use tiFy\Partial\Drivers\DropdownDriver;
use tiFy\Partial\Drivers\DownloaderDriver;
use tiFy\Partial\Drivers\FlashNoticeDriver;
use tiFy\Partial\Drivers\HolderDriver;
use tiFy\Partial\Drivers\ImageLightboxDriver;
use tiFy\Partial\Drivers\MenuDriver;
use tiFy\Partial\Drivers\ModalDriver;
use tiFy\Partial\Drivers\NoticeDriver;
use tiFy\Partial\Drivers\PaginationDriver;
use tiFy\Partial\Drivers\PdfviewerDriver;
use tiFy\Partial\Drivers\ProgressDriver;
use tiFy\Partial\Drivers\SidebarDriver;
use tiFy\Partial\Drivers\SliderDriver;
use tiFy\Partial\Drivers\SpinnerDriver;
use tiFy\Partial\Drivers\TabDriver;
use tiFy\Partial\Drivers\TableDriver;
use tiFy\Partial\Drivers\TagDriver;
use tiFy\Contracts\Routing\Route;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Storage;

class Partial implements PartialContract
{
    use ContainerAwareTrait;
    use BootableTrait;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    private $configBag;

    /**
     * Définition des pilotes par défaut.
     * @var array
     */
    private $defaultDrivers = [
        'accordion'      => AccordionDriver::class,
        'breadcrumb'     => BreadcrumbDriver::class,
        'burger-button'  => BurgerButtonDriver::class,
        'cookie-notice'  => CookieNoticeDriver::class,
        'curtain-menu'   => CurtainMenuDriver::class,
        'dropdown'       => DropdownDriver::class,
        'downloader'     => DownloaderDriver::class,
        'flash-notice'   => FlashNoticeDriver::class,
        'holder'         => HolderDriver::class,
        'image-lightbox' => ImageLightboxDriver::class,
        'menu'           => MenuDriver::class,
        'modal'          => ModalDriver::class,
        'notice'         => NoticeDriver::class,
        'pagination'     => PaginationDriver::class,
        'pdfviewer'      => PdfviewerDriver::class,
        'progress'       => ProgressDriver::class,
        'sidebar'        => SidebarDriver::class,
        'slider'         => SliderDriver::class,
        'spinner'        => SpinnerDriver::class,
        'tab'            => TabDriver::class,
        'table'          => TableDriver::class,
        'tag'            => TagDriver::class
    ];

    /**
     * Liste des instance de pilote chargés.
     * @var PartialDriverInterface[][]
     */
    private $drivers = [];

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
     * Liste des pilotes déclarés.
     * @var PartialDriverInterface[][]|Closure[][]|string[][]|array
     */
    protected $driverDefinitions = [];

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
    public static function instance(): PartialContract
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
    public function boot(): PartialContract
    {
        if (!$this->isBooted()) {
            events()->trigger('partial.booting', [$this]);

            $this->xhrRoute = Router::xhr(
                md5('PartialManager') . '/{partial}/{controller}',
                [$this, 'xhrResponseDispatcher']
            );

            $this->registerDefaultDrivers();

            $this->setBooted();

            events()->trigger('partial.booted', [$this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if (!isset($this->configBag) || is_null($this->configBag)) {
            $this->configBag = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->configBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->configBag->set($key);
        } else {
            return $this->configBag;
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias, $idOrParams = null, ?array $params = []): ?PartialDriverInterface
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
     * @return PartialDriverInterface|null
     */
    protected function getDriverFromDefinition(string $alias): ?PartialDriverInterface
    {
        if (!$def = $this->driverDefinitions[$alias] ?? null) {
            throw new InvalidArgumentException(sprintf('Partial with alias [%s] unavailable', $alias));
        }

        if ($def instanceof PartialDriverInterface) {
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
    public function register(string $alias, $driverDefinition, ?Closure $callback = null): PartialContract
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
    public function registerDefaultDrivers(): PartialContract
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
    public function setConfig(array $attrs): PartialContract
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