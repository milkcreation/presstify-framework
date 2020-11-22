<?php declare(strict_types=1);

namespace tiFy\Partial;

use Exception;
use InvalidArgumentException;
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
use tiFy\Contracts\Partial\Partial as PartialContract;
use tiFy\Contracts\Partial\PartialDriver;
use tiFy\Contracts\Partial\Pdfviewer;
use tiFy\Contracts\Partial\Progress;
use tiFy\Contracts\Partial\Sidebar;
use tiFy\Contracts\Partial\Slider;
use tiFy\Contracts\Partial\Spinner;
use tiFy\Contracts\Partial\Tab;
use tiFy\Contracts\Partial\Table;
use tiFy\Contracts\Partial\Tag;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Storage;

class Partial implements PartialContract
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
     * Liste des éléments déclarées.
     * @var PartialDriver[]
     */
    private $drivers = [];

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
     * Liste des indices courant des pilotes déclarées par alias de qualification.
     * @var int[]
     */
    protected $indexes = [];

    /**
     * Instances des pilotes initiés par alias de qualification et indexés par identifiant de qualification.
     * @var PartialDriver[][]
     */
    protected $instances = [];

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

        throw new Exception(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * Déclaration d'un pilote.
     *
     * @param string $alias
     * @param PartialDriver $driver
     *
     * @throws Exception
     */
    private function _registerDriver(string $alias, PartialDriver $driver): void
    {
        if (isset($this->drivers[$alias]) || isset($this->instances[$alias]) || isset($this->indexes[$alias])) {
            throw new Exception(sprintf('Partial alias [%s] already registered', $alias));
        }

        $this->drivers[$alias] = $driver->build($alias, $this);
        $this->instances[$alias] = [$driver];
        $this->indexes[$alias] = 0;
    }

    /**
     * @inheritDoc
     */
    public function boot(): PartialContract
    {
        if (!$this->booted) {
            $this->registerDefaultDrivers();

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias, $idOrAttrs = null, ?array $attrs = null): ?PartialDriver
    {
        if (!isset($this->drivers[$alias])) {
            throw new InvalidArgumentException(sprintf('Partial with alias [%s] unavailable', $alias));
        }

        if (is_array($idOrAttrs)) {
            $attrs = $idOrAttrs;
            $id = null;
        } else {
            $attrs = $attrs ?: [];
            $id = $idOrAttrs;
        }

        if ($id) {
            if (isset($this->instances[$alias][$id])) {
                return $this->instances[$alias][$id];
            }

            $this->indexes[$alias]++;
            $this->instances[$alias][$id] = clone $this->drivers[$alias];
            $partial = $this->instances[$alias][$id];
        } else {
            $this->indexes[$alias]++;
            $partial = clone $this->drivers[$alias];
        }

        return $partial
            ->setIndex($this->indexes[$alias])
            ->setId($id ?? $alias . $this->indexes[$alias])
            ->set($attrs)->parse();
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
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function register(string $alias, PartialDriver $driver): PartialDriver
    {
        $this->_registerDriver($alias, $driver);

        return $driver;
    }

    /**
     * @inheritDoc
     */
    public function registerDefaultDrivers(): PartialContract
    {
        foreach ($this->defaultDrivers as $name => $alias) {
            $this->_registerDriver($name, $this->getContainer()->get($alias));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $alias)
    {
        return ($container = $this->getContainer()) ? $container->get("partial.{$alias}") : null;
    }

    /**
     * @inheritDoc
     */
    public function resolvable(string $alias): bool
    {
        return ($container = $this->getContainer()) && $container->has("partial.{$alias}");
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
    public function setContainer(Container $container): PartialContract
    {
        $this->container = $container;

        return $this;
    }
}