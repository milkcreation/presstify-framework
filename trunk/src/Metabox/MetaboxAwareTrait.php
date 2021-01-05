<?php

declare(strict_types=1);

namespace tiFy\Metabox;

use Exception;
use tiFy\Metabox\Contracts\MetaboxContract;

trait MetaboxAwareTrait
{
    /**
     * Instance du gestionnaire.
     * @var MetaboxContract|null
     */
    private $metaboxManager;

    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return MetaboxContract|null
     */
    public function metaboxManager(): ?MetaboxContract
    {
        if (is_null($this->metaboxManager)) {
            try {
                $this->metaboxManager = Metabox::instance();
            } catch (Exception $e) {
                $this->metaboxManager;
            }
        }
        return $this->metaboxManager;
    }

    /**
     * Définition de l'instance du gestionnaire.
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