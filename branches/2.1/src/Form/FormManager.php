<?php declare(strict_types=1);

namespace tiFy\Form;

use Exception, LogicException;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Form\AddonDriver as AddonDriverContract;
use tiFy\Contracts\Form\ButtonDriver as ButtonDriverContract;
use tiFy\Contracts\Form\FieldDriver as FieldDriverContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Contracts\Form\FormManager as FormManagerContract;
use tiFy\Contracts\Form\HtmlFieldDriver as HtmlFieldDriverContract;
use tiFy\Contracts\Form\MailerAddonDriver as MailerAddonDriverContract;
use tiFy\Contracts\Form\RecordAddonDriver as RecordAddonDriverContract;
use tiFy\Contracts\Form\SubmitButtonDriver as SubmitButtonDriverContract;
use tiFy\Contracts\Form\TagFieldDriver as TagFieldDriverContract;
use tiFy\Contracts\Form\UserAddonDriver as UserAddonDriverContract;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Storage;

class FormManager implements FormManagerContract
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
     * Liste des pilotes d'addons par défaut.
     * @var string[]
     */
    private $defaultAddonDrivers = [
        'mailer' => MailerAddonDriverContract::class,
        'record' => RecordAddonDriverContract::class,
        'user'   => UserAddonDriverContract::class,
    ];

    /**
     * Liste des pilotes de boutons par défaut.
     * @var string[]
     */
    private $defaultButtonDrivers = [
        'submit' => SubmitButtonDriverContract::class,
    ];

    /**
     * Liste des pilotes de champs par défaut.
     * @var string[]
     */
    private $defaultFieldDrivers = [
        'html' => HtmlFieldDriverContract::class,
        'tag'  => TagFieldDriverContract::class,
    ];

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
     * Liste des formulaires déclarés.
     * @var AddonDriverContract[]|array
     */
    protected $registeredAddonDrivers = [];

    /**
     * Liste des formulaires déclarés.
     * @var ButtonDriverContract[]|array
     */
    protected $registeredButtonDrivers = [];

    /**
     * Liste des formulaires déclarés.
     * @var FieldDriverContract[]|array
     */
    protected $registeredFieldDrivers = [];

    /**
     * Liste des formulaires déclarés.
     * @var FormFactoryContract[]
     */
    protected $registeredForms = [];

    /**
     * Instance du formulaire courant.
     * @var FormFactoryContract|null
     */
    protected $currentForm;

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
    public static function instance(): FormManagerContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new Exception(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->registeredForms;
    }

    /**
     * @inheritDoc
     */
    public function boot(): FormManagerContract
    {
        if ($this->booted === false) {
            foreach ($this->defaultAddonDrivers as $alias => $abstract) {
                if ($this->resolvable($abstract)) {
                    /** @var AddonDriverContract $driver */
                    $driver = $this->resolve($abstract);
                    $this->setAddonDriver($alias, $driver);
                }
            }

            foreach ($this->defaultButtonDrivers as $alias => $abstract) {
                if ($this->resolvable($abstract)) {
                    /** @var ButtonDriverContract $driver */
                    $driver = $this->resolve($abstract);
                    $this->setButtonDriver($alias, $driver);
                }
            }

            foreach ($this->defaultFieldDrivers as $alias => $abstract) {
                if ($this->resolvable($abstract)) {
                    /** @var FieldDriverContract $driver */
                    $driver = $this->resolve($abstract);
                    $this->setFieldDriver($alias, $driver);
                }
            }

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
        }
        if (is_array($key)) {
            return $this->config->set($key);
        }
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function current($formDefinition = null): ?FormFactoryContract
    {
        if (is_null($formDefinition)) {
            return $this->currentForm;
        }

        if (is_string($formDefinition)) {
            $formDefinition = $this->get($formDefinition);
        }

        if (!$formDefinition instanceof FormFactoryContract) {
            return null;
        }

        $this->currentForm = $formDefinition;

        $this->currentForm->onSetCurrent();

        return $this->currentForm;
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?FormFactoryContract
    {
        return $this->registeredForms[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getAddonDriver(string $alias): ?AddonDriverContract
    {
        if (!empty($this->registeredAddonDrivers[$alias])) {
            return clone $this->registeredAddonDrivers[$alias];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getButtonDriver(string $alias): ?ButtonDriverContract
    {
        if (!empty($this->registeredButtonDrivers[$alias])) {
            return clone $this->registeredButtonDrivers[$alias];
        }
        return null;
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
    public function getFieldDriver(string $alias): ?FieldDriverContract
    {
        if (!empty($this->registeredFieldDrivers[$alias])) {
            return clone $this->registeredFieldDrivers[$alias];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getIndex(string $alias): int
    {
        $index = array_search($alias, array_keys($this->registeredForms), true);

        if ($index !== false) {
            return $index;
        }

        throw new LogicException('Unable to retreive registeredForm index');
    }

    /**
     * @inheritDoc
     */
    public function register(string $alias, $formDefinition = []): FormFactoryContract
    {
        $params = [];

        if (!$formDefinition instanceof FormFactoryContract) {
            $params = $formDefinition;

            $form = $this->resolvable(FormFactoryContract::class)
                ? $this->resolve(FormFactoryContract::class) : new BaseFormFactory();
        } else {
            $form = $formDefinition;
        }

        if (!$form instanceof FormFactoryContract) {
            throw new LogicException('Invalid Form Declaration');
        }

        return $this->registeredForms[$alias] = $form
            ->setAlias($alias)
            ->setFormManager($this)
            ->setParams($params)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function registerAddonDriver(string $alias, $addonDefinition = []): AddonDriverContract
    {
        $params = [];

        if (!$addonDefinition instanceof AddonDriverContract) {
            $params = $addonDefinition;

            $addon = $this->resolvable(AddonDriverContract::class)
                ? $this->resolve(AddonDriverContract::class) : new AddonDriver();
        } else {
            $addon = $addonDefinition;
        }

        if (!$addon instanceof AddonDriverContract) {
            throw new LogicException('Invalid AddonDriver Declaration');
        }

        return $this->registeredAddonDrivers[$alias] = $addon
            ->setAlias($alias)
            ->setParams($params)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function registerButtonDriver(string $alias, $buttonDefinition = []): ButtonDriverContract
    {
        $params = [];

        if (!$buttonDefinition instanceof ButtonDriverContract) {
            $params = $buttonDefinition;

            $button = $this->resolvable(ButtonDriverContract::class)
                ? $this->resolve(ButtonDriverContract::class) : new ButtonDriver();
        } else {
            $button = $buttonDefinition;
        }

        if (!$button instanceof ButtonDriverContract) {
            throw new LogicException('Invalid ButtonDriver Declaration');
        }

        return $this->registeredButtonDrivers[$alias] = $button
            ->setAlias($alias)
            ->setParams($params)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function registerFieldDriver(string $alias, $fieldDefinition = []): FieldDriverContract
    {
        $params = [];

        if (!$fieldDefinition instanceof FieldDriverContract) {
            $params = $fieldDefinition;

            $field = $this->resolvable(FieldDriverContract::class)
                ? $this->resolve(FieldDriverContract::class) : new FieldDriver();
        } else {
            $field = $fieldDefinition;
        }

        if (!$field instanceof FieldDriverContract) {
            throw new LogicException('Invalid FieldDriver Declaration');
        }

        return $this->registeredFieldDrivers[$alias] = $field
            ->setAlias($alias)
            ->setParams($params)
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function reset(): FormManagerContract
    {
        if ($this->currentForm instanceof FormFactoryContract) {
            $this->currentForm->onResetCurrent();
        }

        $this->currentForm = null;

        return $this;
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
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(__DIR__ . '/Resources');
        }

        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function setAddonDriver(string $alias, AddonDriverContract $driver): FormManagerContract
    {
        $this->registerAddonDriver($alias, $driver);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setButtonDriver(string $alias, ButtonDriverContract $driver): FormManagerContract
    {
        $this->registerButtonDriver($alias, $driver);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): FormManagerContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container): FormManagerContract
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFieldDriver(string $alias, FieldDriverContract $driver): FormManagerContract
    {
        $this->registerFieldDriver($alias, $driver);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setForm(string $alias, FormFactoryContract $factory): FormManagerContract
    {
        $this->register($alias, $factory);

        return $this;
    }
}