<?php declare(strict_types=1);

namespace tiFy\Partial;

use Closure;
use BadMethodCallException, Exception;
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Contracts\Partial\PartialDriver as PartialDriverContract;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\Proxy\View;
use tiFy\Support\Str;

/**
 * @mixin \tiFy\Support\ParamsBag
 */
class PartialDriver implements PartialDriverContract
{
    use BootableTrait, ParamsBagTrait;

    /**
     * Indice de l'instance dans le gestionnaire.
     * @var int
     */
    private $index = 0;

    /**
     * Instance du gestionnaire.
     * @var Partial
     */
    private $partialManager;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

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
     * @param PartialManager $partialManager
     */
    public function __construct(PartialManager $partialManager)
    {
        $this->partialManager = $partialManager;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $key)
    {
        return $this->params($key);
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->params()->{$method}(...$arguments);
        } catch(Exception $e) {
            throw new BadMethodCallException(sprintf(
                'Partial [%s] method call [%s] throws an exception: %s',
                $this->getAlias(), $method, $e->getMessage()
            ));
        }
    }

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
    public function after(): void
    {
        echo ($after = $this->get('after')) instanceof Closure ? $after($this) : $after;
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
        echo ($before = $this->get('before')) instanceof Closure ? $before($this) : $before;
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            $this->parseParams();

            $this->setBooted();
        }
    }

    /**
     * @inheritDoc
     */
    public function content(): void
    {
        echo ($content = $this->get('content')) instanceof Closure ? $content($this) : $content;
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function defaultParams(): array
    {
        return array_merge(static::$defaults, [
            /**
             * @var array $attrs Attributs HTML du champ.
             */
            'attrs'    => [],
            /**
             * @var string $after Contenu placé après le champ.
             */
            'after'    => '',
            /**
             * @var string $before Contenu placé avant le champ.
             */
            'before'   => '',
            /**
             * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
             */
            'viewer'   => [],
            /**
             * @var Closure|array|string|null
             */
            'render'   => null
        ]);
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
    public function parseParams(): PartialDriverContract
    {
        return $this->parseAttrId()->parseAttrClass();
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
            $this->set('attrs.class', sprintf($this->get('attrs.class'), $default_class));
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
    public function partialManager(): PartialManager
    {
        return $this->partialManager;
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
    public function setAlias(string $alias): PartialDriverContract
    {
        $this->alias = $alias;

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
            $this->viewEngine = $this->partialManager()->containerHas('partial.view-engine') ?
                $this->partialManager()->containerGet('partial.view-engine') : View::getPlatesEngine();

            $defaultConfig = $this->partialManager()->config('default.driver.viewer', []);

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
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->partialManager()->resources("/views/{$this->getAlias()}");
    }

    /**
     * Contrôleur de traitement des requêtes XHR.
     *
     * @param array ...$args
     *
     * @return array
     */
    public function xhrResponse(...$args): array
    {
        return [
            'success' => true
        ];
    }
}