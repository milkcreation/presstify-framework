<?php

declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Router;

class MetaboxScreen implements MetaboxScreenInterface
{
    use BootableTrait;
    use ParamsBagTrait;
    use MetaboxAwareTrait;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Indicateur d'écran d'affichage courant.
     * @var bool|null
     */
    protected $current;

    /**
     * @param MetaboxContract $metaboxManager
     */
    public function __construct(MetaboxContract $metaboxManager)
    {
        $this->setMetaboxManager($metaboxManager);
    }

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxScreenInterface
    {
        if (!$this->isBooted()) {
            events()->trigger('metabox.screen.booted', [$this->getAlias(), $this]);

            $this->parseParams();

            $this->setBooted();

            events()->trigger('metabox.driver.booting', [$this->getAlias(), $this]);
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
    public function setAlias(string $alias): MetaboxScreenInterface
    {
        $this->alias = $alias;

        return $this;
    }
}