<?php

declare(strict_types=1);

namespace tiFy\Support\Concerns;

use Exception;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\Partial;

trait PartialManagerAwareTrait
{
    /**
     * Instance du gestionnaire de portions d'affichage.
     * @var PartialContract
     */
    private $partialManager;

    /**
     * Instance du gestionnaire de portions d'affichage.
     *
     * @return PartialContract
     */
    public function partialManager(): PartialContract
    {
        if ($this->partialManager === null) {
            if ($this instanceof ContainerAwareTrait && $this->containerHas(PartialContract::class)) {
                $this->partialManager = $this->containerGet(PartialContract::class);
            } else {
                try {
                    $this->partialManager = Partial::instance();
                } catch(Exception $e) {
                    $this->partialManager = new Partial();
                }
            }
        }

        return $this->partialManager;
    }

    /**
     * Définition du gestionnaire de portion d'affichage.
     *
     * @param PartialContract $partialManager
     *
     * @return static
     */
    public function setPartialManager(PartialContract $partialManager): self
    {
        $this->partialManager = $partialManager;

        return $this;
    }
}