<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\Metabox;
use tiFy\Contracts\Metabox\MetaboxDriver;
use tiFy\Contracts\Metabox\MetaboxScreen as MetaboxScreenContract;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Router;

class MetaboxScreen extends ParamsBag implements MetaboxScreenContract
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
     * Liste des pilotes déclarés.
     * @var MetaboxDriver[]|array
     */
    protected $drivers = [];

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Indicateur d'écran d'affichage courant.
     * @var boolean|null
     */
    protected $current;

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxScreenContract
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
    public function build(): MetaboxScreenContract
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
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * @inheritDoc
     */
    public function isCurrent(): bool
    {
        if (is_null($this->current)) {
            if ($this->isCurrentRoute()) {
                $this->current = true;
            } elseif ($this->isCurrentRequest()) {
                $this->current = true;
            } else {
                $this->current = false;
            }
        }

        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function isCurrentRoute(): bool
    {
        return Router::hasCurrent() && (Router::currentRouteName() === $this->alias);
    }

    /**
     * @inheritDoc
     */
    public function isCurrentRequest(): bool
    {
        return ltrim(rtrim(Request::getPathInfo(), '/'), '/') === ltrim(rtrim($this->alias, '/'), '/');
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
    public function parse(): MetaboxScreenContract
    {
        $this->attributes = array_merge(
            $this->defaults(), $this->metabox()->config("screen.{$this->getAlias()}", []), $this->attributes
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): MetaboxScreenContract
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDriver(MetaboxDriver $driver): MetaboxScreenContract
    {
        $this->drivers[$driver->getUuid()] = $driver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetabox(Metabox $metabox): MetaboxScreenContract
    {
        $this->metabox = $metabox;

        return $this;
    }
}