<?php declare(strict_types=1);

namespace tiFy\Field;

use Closure;
use tiFy\Contracts\Field\Field;
use tiFy\Contracts\Field\FieldDriver as FieldDriverContract;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\ParamsBag;
use tiFy\Support\Str;

abstract class FieldDriver extends ParamsBag implements FieldDriverContract
{
    /**
     * Alias de qualification.
     * @var string|null
     */
    private $alias;

    /**
     * Indicateur d'initialisation.
     * @var string
     */
    private $built = false;

    /**
     * Instance du gestionnaire.
     * @var Field
     */
    private $field;

    /**
     * Indice de l'instance dans le gestionnaire.
     * @var int
     */
    private $index = 0;

    /**
     * Liste des attributs par défaut.
     * @var array
     */
    protected static $defaults = [];

    /**
     * Identifiant de qualification.
     * {@internal par défaut concaténation de l'alias et de l'indice.}
     * @var string
     */
    protected $id = '';

    /**
     * Instance du moteur de gabarits d'affichage.
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
     *
     * @return $this
     */
    public function build(string $alias, Field $field): FieldDriverContract
    {
        if (!$this->built) {
            $this->alias = $alias;
            $this->field = $field;

            $this->boot();

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after(): void
    {
        echo ($after = $this->get('after', '')) instanceof Closure ? $after($this) : $after;
    }

    /**
     * @inheritDoc
     */
    public function attrs(): void
    {
        echo HtmlAttrs::createFromAttrs($this->get('attrs', []));
    }

    /**
     * @inheritDoc
     */
    public function before(): void
    {
        echo ($before = $this->get('before', '')) instanceof Closure ? $before($this) : $before;
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function content(): void
    {
        echo ($content = $this->get('content', '')) instanceof Closure ? $content($this) : $content;
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function defaults(): array
    {
        return array_merge(static::$defaults, []);
    }

    /**
     * @inheritDoc
     */
    public function field(): ?Field
    {
        return $this->field;
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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->get('attrs.name', '') ?: $this->get('name');
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->get('value', null);
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parse(): FieldDriverContract
    {
        $this->attributes = array_merge(
            $this->defaults(), $this->field()->config($this->getAlias(), []), $this->attributes
        );

        $this->parseDefaults();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrClass(): FieldDriverContract
    {
        $base = 'Field' . ucfirst(preg_replace('/\./', '-', Str::camel($this->getAlias())));

        $default_class = "{$base} {$base}--" . $this->getIndex();
        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        }

        if (!$this->get('attrs.class')) {
            $this->forget('attrs.class');
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parseAttrId(): FieldDriverContract
    {
        if (!$this->get('attrs.id')) {
            $this->forget('attrs.id');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrName(): FieldDriverContract
    {
        if ($name = $this->get('name')) {
            $this->set('attrs.name', $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrValue(): FieldDriverContract
    {
        if ($value = $this->get('value')) {
            $this->set('attrs.value', $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseDefaults(): FieldDriverContract
    {
        return $this->parseAttrId()->parseAttrClass()->parseAttrName()->parseAttrValue()->parseViewer();
    }

    /**
     * @inheritDoc
     */
    public function parseViewer(): FieldDriverContract
    {
        foreach ($this->get('viewer', []) as $key => $value) {
            $this->view()->params([$key => $value]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->view('index', $this->all());
    }

    /**
     * @inheritDoc
     */
    public static function setDefaults(array $defaults = []): void
    {
        static::$defaults = $defaults;
    }

    /**
     * @inheritDoc
     */
    public function setId(string $id): FieldDriverContract
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(int $index): FieldDriverContract
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setViewEngine(ViewEngine $viewer): FieldDriverContract
    {
        $this->viewEngine = $viewer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->field()->resolve('view-engine');

            $defaultConfig = $this->field()->config('_default.viewer', []);

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

            $config = $this->field()->config("{$this->getAlias()}.viewer", []);

            $this->viewEngine->params(array_merge([
                'directory'    => $this->viewDirectory(),
                'factory'      => FieldView::class,
                'field'      => $this
            ], $defaultConfig, $config, $this->get('viewer', [])));
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }

    /**
     * Chemin absolu du répertoire des gabarits d'affichage.
     *
     * @return string
     */
    public function viewDirectory(): string
    {
        return $this->field()->resources("/views/{$this->getAlias()}");
    }
}