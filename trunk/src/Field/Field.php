<?php

declare(strict_types=1);

namespace tiFy\Field;

use Closure;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Routing\Route;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Field\Drivers\ButtonDriver;
use tiFy\Field\Drivers\CheckboxCollectionDriver;
use tiFy\Field\Drivers\CheckboxDriver;
use tiFy\Field\Drivers\ColorpickerDriver;
use tiFy\Field\Drivers\DatepickerDriver;
use tiFy\Field\Drivers\DatetimeJsDriver;
use tiFy\Field\Drivers\FileDriver;
use tiFy\Field\Drivers\FileJsDriver;
use tiFy\Field\Drivers\HiddenDriver;
use tiFy\Field\Drivers\LabelDriver;
use tiFy\Field\Drivers\NumberDriver;
use tiFy\Field\Drivers\NumberJsDriver;
use tiFy\Field\Drivers\PasswordDriver;
use tiFy\Field\Drivers\PasswordJsDriver;
use tiFy\Field\Drivers\RadioCollectionDriver;
use tiFy\Field\Drivers\RadioDriver;
use tiFy\Field\Drivers\RepeaterDriver;
use tiFy\Field\Drivers\RequiredDriver;
use tiFy\Field\Drivers\SelectDriver;
use tiFy\Field\Drivers\SelectImageDriver;
use tiFy\Field\Drivers\SelectJsDriver;
use tiFy\Field\Drivers\SubmitDriver;
use tiFy\Field\Drivers\SuggestDriver;
use tiFy\Field\Drivers\TextareaDriver;
use tiFy\Field\Drivers\TextDriver;
use tiFy\Field\Drivers\TextRemainingDriver;
use tiFy\Field\Drivers\TinymceDriver;
use tiFy\Field\Drivers\ToggleSwitchDriver;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Storage;

class Field implements FieldContract
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
        'button'              => ButtonDriver::class,
        'checkbox'            => CheckboxDriver::class,
        'checkbox-collection' => CheckboxCollectionDriver::class,
        'colorpicker'         => ColorpickerDriver::class,
        'datepicker'          => DatepickerDriver::class,
        'datetime-js'         => DatetimeJsDriver::class,
        'file'                => FileDriver::class,
        'file-js'             => FileJsDriver::class,
        'hidden'              => HiddenDriver::class,
        'label'               => LabelDriver::class,
        'number'              => NumberDriver::class,
        'number-js'           => NumberJsDriver::class,
        'password'            => PasswordDriver::class,
        'password-js'         => PasswordJsDriver::class,
        'radio'               => RadioDriver::class,
        'radio-collection'    => RadioCollectionDriver::class,
        'repeater'            => RepeaterDriver::class,
        'required'            => RequiredDriver::class,
        'select'              => SelectDriver::class,
        'select-image'        => SelectImageDriver::class,
        'select-js'           => SelectJsDriver::class,
        'submit'              => SubmitDriver::class,
        'suggest'             => SuggestDriver::class,
        'text'                => TextDriver::class,
        'textarea'            => TextareaDriver::class,
        'text-remaining'      => TextRemainingDriver::class,
        'tinymce'             => TinymceDriver::class,
        'toggle-switch'       => ToggleSwitchDriver::class,
    ];

    /**
     * Liste des éléments déclarées.
     * @var FieldDriver[]
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
     * @var FieldDriver[][]|Closure[][]|string[][]|array
     */
    protected $driverDefinitions = [];

    /**
     * @param array $config
     * @param Container|null $container
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
    public static function instance(): FieldContract
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
    public function boot(): FieldContract
    {
        if (!$this->isBooted()) {
            events()->trigger('field.booting', [$this]);

            $this->xhrRoute = Router::xhr(
                md5('field') . '/{field}/{controller}',
                [$this, 'xhrResponseDispatcher']
            );

            $this->registerDefaultDrivers();

            $this->setBooted();

            events()->trigger('field.booted', [$this]);
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
    public function get(string $alias, $idOrParams = null, ?array $params = []): ?FieldDriverInterface
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
     * @return FieldDriver|null
     */
    protected function getDriverFromDefinition(string $alias): ?FieldDriver
    {
        if (!$def = $this->driverDefinitions[$alias] ?? null) {
            throw new InvalidArgumentException(sprintf('Field with alias [%s] unavailable', $alias));
        }

        if ($def instanceof FieldDriver) {
            return clone $def;
        } elseif (is_string($def) && $this->containerHas($def)) {
            return $this->containerGet($def);
        } elseif (is_string($def) && class_exists($def)) {
            return new $def($this);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getXhrRouteUrl(string $field, ?string $controller = null, array $params = []): string
    {
        $controller = $controller ?? 'xhrResponse';

        return $this->xhrRoute->getUrl(array_merge($params, compact('field', 'controller')));
    }

    /**
     * @inheritDoc
     */
    public function register(string $alias, $driverDefinition, ?Closure $callback = null): FieldContract
    {
        if (isset($this->driverDefinitions[$alias])) {
            throw new RuntimeException(sprintf('Another FieldDriver with alias [%s] already registered', $alias));
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
    public function registerDefaultDrivers(): FieldContract
    {
        foreach ($this->defaultDrivers as $alias => $driverDefinition) {
            $this->register($alias, $driverDefinition);
        }
        return $this;
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
    public function setConfig(array $attrs): FieldContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseDispatcher(string $field, string $controller, ...$args): array
    {
        try {
            $driver = $this->get($field);
        } catch (Exception $e) {
            throw new NotFoundException(
                sprintf('FieldDriver [%s] return exception : %s', $field, $e->getMessage())
            );
        }

        try {
            return $driver->{$controller}(...$args);
        } catch (Exception $e) {
            throw new NotFoundException(
                sprintf('FieldDriver [%s] Controller [%s] call return exception', $controller, $field)
            );
        }
    }
}