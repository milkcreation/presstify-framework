<?php declare(strict_types=1);

namespace tiFy\Metabox;

use Closure;
use Exception;
use Ramsey\Uuid\Uuid;
use tiFy\Contracts\Metabox\MetaboxContext;
use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Contracts\Metabox\Metabox;
use tiFy\Contracts\Metabox\MetaboxScreen;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\Arr;
use tiFy\Support\ParamsBag;

class MetaboxDriver extends ParamsBag implements MetaboxDriverContract
{
    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Indicateur d'intialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Instance du contexte d'affichage.
     * @var MetaboxContext|null
     */
    private $context;

    /**
     * Instance du gestionnaire de metaboxes.
     * @var Metabox|null
     */
    private $metabox;

    /**
     * Instance de l'écran d'affichage.
     * @var MetaboxScreen|null
     */
    private $screen;

    /**
     * Identifiant de qualification unique.
     * @var string;
     */
    private $uuid;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Liste des arguments dynamiques passée en paramètres à l'appel du rendu.
     * @var array
     */
    protected $args = [];

    /**
     * Liste de fonction anonyme de traitement
     * @var Closure[]
     */
    protected $handlers = [];

    /**
     * Valeur par défaut.
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Valeur courante.
     * @var mixed
     */
    protected $value;

    /**
     * Instance du moteur d'affichage des gabarits.
     * @var ViewEngine
     */
    protected $viewEngine;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxDriverContract
    {
        if (!$this->booted) {
            $this->uuid = Uuid::uuid1()->__toString();

            $this->parse();

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): MetaboxDriverContract
    {
        if (!$this->built) {
            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'name'     => '',
            'parent'   => null,
            'params'   => [],
            'position' => null,
            'title'    => '',
            'value'    => null,
            'render'   => '',
            'viewer'   => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function getContext(): ?MetaboxContext
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function getScreen(): ?MetaboxScreen
    {
        return $this->screen;
    }

    /**
     * @inheritDoc
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $args = []): MetaboxDriverContract
    {
        $this->args = $args;

        foreach ($this->handlers as $handler) {
            $handler($this, ...$this->args);
        }

        $value = $this->get('value');
        $this->value = ($value instanceof Closure) ? $value($this, ...$this->args) : $value;

        $default = $this->defaultValue();
        if (is_array($default)) {
            $this->value = array_merge($default, $this->value ?: []);
        } elseif (is_null($this->value)) {
            $this->value = $default;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function metabox(): ?Metabox
    {
        return $this->metabox;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->get('name');
    }

    /**
     * @inheritDoc
     */
    public function params($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->get('params', []);
        } elseif (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set("params.{$k}", $v);
            }

            return $this;
        } else {
            return $this->get("params.{$key}", $default);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parse(): ?MetaboxDriverContract
    {
        $this->attributes = array_merge(
            $this->defaults(), $this->metabox()->config("driver.{$this->getAlias()}", []), $this->attributes
        );

        $this->params(array_merge($this->defaultParams(), $this->get('params', [])));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $render = $this->get('render', '');

        if ($render instanceof Closure) {
            $render = (string)$render($this, ...$this->args);
        }

        return $render ?: ($this->view()->exists('index') ? $this->view('index', $this->all()) : '');
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): MetaboxDriverContract
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContext(string $alias): MetaboxDriverContract
    {
        try {
            $this->context = ($context = $this->metabox()->getRegisteredContext($alias))
                ? $this->metabox()->addContext($alias, $context) : $this->metabox()->addContext($alias);

            $this->context->setDriver($this);
        } catch (Exception $e) {
            unset($e);
            $this->context = null;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultValue($value = null): MetaboxDriverContract
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHandler(Closure $func): MetaboxDriverContract
    {
        $this->handlers[] = $func;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetabox(Metabox $metabox): MetaboxDriverContract
    {
        $this->metabox = $metabox;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScreen(string $alias): MetaboxDriverContract
    {
        try {
            $this->screen = ($screen = $this->metabox()->getScreen($alias))
                ? $this->metabox()->addScreen($alias, $screen) : $this->metabox()->addScreen($alias);

            $this->screen->setDriver($this);
        } catch (Exception $e) {
            unset($e);
            $this->screen = null;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return $this->view()->exists('title') ? (string)$this->view('title', $this->all()) : $this->get('title', '');
    }

    /**
     * @inheritDoc
     */
    public function value(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->value;
        } elseif (is_array($this->value)) {
            return Arr::get($this->value, $key, $default);
        } else {
            return $default;
        }
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->metabox()->resolve('view-engine.driver');

            $defaultConfig = $this->metabox()->config('default.driver.viewer', []);

            if (isset($defaultConfig['directory'])) {
                $defaultConfig['directory'] = rtrim($defaultConfig['directory'], '/') . '/' . $this->getAlias();

                if (!file_exists($defaultConfig['directory'])) {
                    unset($defaultConfig['directory']);
                }
            }

            if (isset($defaultConfig['override_dir'])) {
                $defaultConfig['override_dir'] = rtrim($defaultConfig['override_dir'], '/') . '/' . $this->getAlias();

                if (!file_exists($defaultConfig['override_dir'])) {
                    unset($defaultConfig['override_dir']);
                }
            }

            $this->viewEngine->params(array_merge([
                'directory' => $this->viewDirectory(),
                'factory'   => MetaboxView::class,
                'driver'    => $this,
            ], $defaultConfig, $this->get('viewer', [])));
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources("/views/driver/{$this->getAlias()}");
    }
}