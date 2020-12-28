<?php

declare(strict_types=1);

namespace tiFy\Field;

use Closure;
use BadMethodCallException;
use Exception;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\Proxy\View;
use tiFy\Support\Str;

/**
 * @mixin \tiFy\Support\ParamsBag
 */
class FieldDriver implements FieldDriverInterface
{
    use BootableTrait;
    use ParamsBagTrait;

    /**
     * Indice de l'instance dans le gestionnaire.
     * @var int
     */
    private $index = 0;

    /**
     * Instance du gestionnaire.
     * @var FieldContract
     */
    private $fieldManager;

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
     * @param FieldContract $fieldManager
     */
    public function __construct(FieldContract $fieldManager)
    {
        $this->fieldManager = $fieldManager;
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
        } catch (Exception $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Field [%s] method call [%s] throws an exception: %s',
                    $this->getAlias(),
                    $method,
                    $e->getMessage()
                )
            );
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
            events()->trigger('field.driver.booting', [$this->getAlias(), $this]);

            $this->parseParams();

            $this->setBooted();

            events()->trigger('field.driver.booted', [$this->getAlias(), $this]);
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
        return array_merge(
            static::$defaults,
            [
                /**
                 * @var array $attrs Attributs HTML du champ.
                 */
                'attrs'  => [],
                /**
                 * @var string $after Contenu placé après le champ.
                 */
                'after'  => '',
                /**
                 * @var string $before Contenu placé avant le champ.
                 */
                'before' => '',
                /**
                 * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
                 */
                'viewer' => [],
                /**
                 * @var Closure|array|string|null
                 */
                'render' => null,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function fieldManager(): FieldContract
    {
        return $this->fieldManager;
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
        return $this->get('attrs.name') ?: $this->get('name');
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->get('value', null);
    }

    /**
     * @inheritDoc
     */
    public function getXhrUrl(array $params = []): string
    {
        return $this->fieldManager()->getXhrRouteUrl($this->getAlias(), null, $params);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): FieldDriverInterface
    {
        return $this->parseAttrId()->parseAttrClass()->parseAttrName()->parseAttrValue();
    }

    /**
     * @inheritDoc
     */
    public function parseAttrClass(): FieldDriverInterface
    {
        $base = 'Field' . ucfirst(preg_replace('/\./', '-', Str::camel($this->getAlias())));

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
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parseAttrId(): FieldDriverInterface
    {
        if (!$this->get('attrs.id')) {
            $this->forget('attrs.id');
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrName(): FieldDriverInterface
    {
        if ($name = $this->get('name')) {
            $this->set('attrs.name', $name);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseAttrValue(): FieldDriverInterface
    {
        if ($value = $this->get('value')) {
            $this->set('attrs.value', $value);
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
    public function setAlias(string $alias): FieldDriverInterface
    {
        $this->alias = $alias;

        return $this;
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
    public function setId(string $id): FieldDriverInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(int $index): FieldDriverInterface
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setViewEngine(ViewEngine $viewEngine): FieldDriverInterface
    {
        $this->viewEngine = $viewEngine;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->fieldManager()->containerHas('field.view-engine') ?
                $this->fieldManager()->containerGet('field.view-engine') : View::getPlatesEngine();

            $defaultConfig = $this->fieldManager()->config('default.driver.viewer', []);

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

            $this->viewEngine->params(
                array_merge(
                    [
                        'directory' => $this->viewDirectory(),
                        'factory'   => FieldView::class,
                        'driver'    => $this,
                    ],
                    $defaultConfig,
                    $this->get('viewer', [])
                )
            );
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
        return $this->fieldManager()->resources("/views/{$this->getAlias()}");
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        return [
            'success' => true,
        ];
    }
}