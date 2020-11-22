<?php declare(strict_types=1);

namespace tiFy\Partial;

use Closure;
use tiFy\Contracts\Partial\Partial;
use tiFy\Contracts\Partial\PartialDriver as PartialDriverContract;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\ParamsBag;
use tiFy\Support\Str;

abstract class PartialDriver extends ParamsBag implements PartialDriverContract
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
     * Indice de l'instance dans le gestionnaire.
     * @var int
     */
    private $index = 0;

    /**
     * Instance du gestionnaire.
     * @var Partial
     */
    private $partial;

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
    public function build(string $alias, Partial $partial): PartialDriverContract
    {
        if (!$this->built) {
            $this->alias = $alias;
            $this->partial = $partial;

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
     *
     * @return $this
     */
    public function parse(): PartialDriverContract
    {
        $this->attributes = array_merge(
            $this->defaults(), $this->partial()->config("driver.{$this->getAlias()}", []), $this->attributes
        );

        $this->parseDefaults();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrClass(): PartialDriverContract
    {
        $base = Str::studly($this->getAlias());

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
     * @inheritDoc
     */
    public function parseAttrId(): PartialDriverContract
    {
        if (!$this->get('attrs.id')) {
            $this->forget('attrs.id');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseDefaults(): PartialDriverContract
    {
        return $this->parseAttrId()->parseAttrClass();
    }

    /**
     * @inheritDoc
     */
    public function partial(): ?Partial
    {
        return $this->partial;
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
    public function setId(string $id): PartialDriverContract
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(int $index): PartialDriverContract
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setViewEngine(ViewEngine $viewer): PartialDriverContract
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
            $this->viewEngine = $this->partial()->resolve('view-engine');

            $defaultConfig = $this->partial()->config('default.driver.viewer', []);

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
                'factory'   => PartialView::class,
                'driver'    => $this,
            ], $defaultConfig, $this->get('viewer', [])));
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
        return $this->partial()->resources("/views/{$this->getAlias()}");
    }
}