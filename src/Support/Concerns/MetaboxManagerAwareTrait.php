<?php

declare(strict_types=1);

namespace tiFy\Support\Concerns;

use Exception;
use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Metabox\Metabox;

trait MetaboxManagerAwareTrait
{
    /**
     * Instance du gestionnaire de metaboxes.
     * @var MetaboxContract
     */
    private $metaboxManager;

    /**
     * Instance du gestionnaire de metaboxes.
     *
     * @return MetaboxContract
     */
    public function metaboxManager(): MetaboxContract
    {
        if ($this->metaboxManager === null) {
            if ($this instanceof ContainerAwareTrait && $this->containerHas(MetaboxContract::class)) {
                $this->metaboxManager = $this->containerGet(MetaboxContract::class);
            } else {
                try {
                    $this->metaboxManager = Metabox::instance();
                } catch(Exception $e) {
                    $this->metaboxManager = new Metabox();
                }
            }
        }

        return $this->metaboxManager;
    }

    /**
     * Définition du gestionnaire de metaboxes.
     *
     * @param MetaboxContract $metaboxManager
     *
     * @return static
     */
    public function setMetaboxManager(MetaboxContract $metaboxManager): self
    {
        $this->metaboxManager = $metaboxManager;

        return $this;
    }
}