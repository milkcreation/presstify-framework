<?php declare(strict_types=1);

namespace tiFy\Field;

use Exception;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Field\Button;
use tiFy\Contracts\Field\Checkbox;
use tiFy\Contracts\Field\CheckboxCollection;
use tiFy\Contracts\Field\Colorpicker;
use tiFy\Contracts\Field\Datepicker;
use tiFy\Contracts\Field\DatetimeJs;
use tiFy\Contracts\Field\Field as FieldContract;
use tiFy\Contracts\Field\FieldDriver;
use tiFy\Contracts\Field\File;
use tiFy\Contracts\Field\FileJs;
use tiFy\Contracts\Field\Hidden;
use tiFy\Contracts\Field\Label;
use tiFy\Contracts\Field\Number;
use tiFy\Contracts\Field\NumberJs;
use tiFy\Contracts\Field\Password;
use tiFy\Contracts\Field\PasswordJs;
use tiFy\Contracts\Field\Radio;
use tiFy\Contracts\Field\RadioCollection;
use tiFy\Contracts\Field\Repeater;
use tiFy\Contracts\Field\Required;
use tiFy\Contracts\Field\Select;
use tiFy\Contracts\Field\SelectImage;
use tiFy\Contracts\Field\SelectJs;
use tiFy\Contracts\Field\Submit;
use tiFy\Contracts\Field\Suggest;
use tiFy\Contracts\Field\Text;
use tiFy\Contracts\Field\TextRemaining;
use tiFy\Contracts\Field\Textarea;
use tiFy\Contracts\Field\Tinymce;
use tiFy\Contracts\Field\ToggleSwitch;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Storage;

class Field implements FieldContract
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
        'button'              => Button::class,
        'checkbox'            => Checkbox::class,
        'checkbox-collection' => CheckboxCollection::class,
        'colorpicker'         => Colorpicker::class,
        'datepicker'          => Datepicker::class,
        'datetime-js'         => DatetimeJs::class,
        'file'                => File::class,
        'file-js'             => FileJs::class,
        'hidden'              => Hidden::class,
        'label'               => Label::class,
        'number'              => Number::class,
        'number-js'           => NumberJs::class,
        'password'            => Password::class,
        'password-js'         => PasswordJs::class,
        'radio'               => Radio::class,
        'radio-collection'    => RadioCollection::class,
        'repeater'            => Repeater::class,
        'required'            => Required::class,
        'select'              => Select::class,
        'select-image'        => SelectImage::class,
        'select-js'           => SelectJs::class,
        'submit'              => Submit::class,
        'suggest'             => Suggest::class,
        'text'                => Text::class,
        'textarea'            => Textarea::class,
        'text-remaining'      => TextRemaining::class,
        'tinymce'             => Tinymce::class,
        'toggle-switch'       => ToggleSwitch::class,
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
     * @var FieldDriver[][]
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
    public static function instance(): FieldContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new Exception('Unavailable Field instance');
    }

    /**
     * Déclaration d'un pilote.
     *
     * @param string $alias
     * @param FieldDriver $driver
     *
     * @throws Exception
     */
    private function _registerDriver(string $alias, FieldDriver $driver): void
    {
        if (isset($this->drivers[$alias]) || isset($this->instances[$alias]) || isset($this->indexes[$alias])) {
            throw new Exception(sprintf('Field alias [%s] already registered', $alias));
        }

        $this->drivers[$alias] = $driver->build($alias, $this);
        $this->instances[$alias] = [$driver];
        $this->indexes[$alias] = 0;
    }

    /**
     * @inheritDoc
     */
    public function boot(): FieldContract
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
    public function get(string $alias, $idOrAttrs = null, ?array $attrs = null): ?FieldDriver
    {
        if (!isset($this->drivers[$alias])) {
            throw new InvalidArgumentException(sprintf('Field with alias [%s] unavailable', $alias));
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
            $field = $this->instances[$alias][$id];
        } else {
            $this->indexes[$alias]++;
            $field = clone $this->drivers[$alias];
        }

        return $field
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
    public function register(string $alias, FieldDriver $driver): FieldDriver
    {
        $this->_registerDriver($alias, $driver);

        return $driver;
    }

    /**
     * @inheritDoc
     */
    public function registerDefaultDrivers(): FieldContract
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
        return ($container = $this->getContainer()) ? $container->get("field.{$alias}") : null;
    }

    /**
     * @inheritDoc
     */
    public function resolvable(string $alias): bool
    {
        return ($container = $this->getContainer()) && $container->has("field.{$alias}");
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
    public function setContainer(Container $container): FieldContract
    {
        $this->container = $container;

        return $this;
    }
}