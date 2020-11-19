<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxContext as MetaboxContextContract;
use tiFy\Contracts\Metabox\Metabox;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\ParamsBag;

class MetaboxContext extends ParamsBag implements MetaboxContextContract
{
    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Instance du gestionnaire de metaboxes.
     * @var Metabox|null
     */
    private $metabox;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Instance du gestionnaire de gabarit d'affichage.
     * @var ViewEngine|null
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
    public function boot(): MetaboxContextContract
    {
        if (!$this->booted) {
            $this->parse();

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): MetaboxContextContract
    {
        if (!$this->built) {
            $this->built = true;
        }

        return $this;
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
    public function defaults(): array
    {
        return [
            'items' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function metabox(): ?Metabox
    {
        return $this->metabox;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parse(): MetaboxContextContract
    {
        $this->attributes = array_merge(
            $this->defaults(), $this->metabox()->config("context.{$this->getAlias()}", []), $this->attributes
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set([
            'items' => $this->metabox()->getRenderedDrivers($this->getAlias()),
        ]);

        return $this->view('index', $this->parse()->all());
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): MetaboxContextContract
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetabox(Metabox $metabox): MetaboxContextContract
    {
        $this->metabox = $metabox;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (!$this->viewEngine) {
            $this->viewEngine = $this->metabox()->resolve('view-engine.context');

            $this->viewEngine->params([
                'directory' => $this->metabox()->resources("/views/context/{$this->getAlias()}")
            ]);
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }
}