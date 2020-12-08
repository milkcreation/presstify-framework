<?php declare(strict_types=1);

namespace tiFy\Support\Concerns;

trait BuildableTrait
{
    /**
     * Etat d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Vérification de l'état d'initialisation.
     * @return bool
     */
    public function isBuilt(): bool
    {
        return $this->built;
    }

    /**
     * Définition de l'état de chargement.
     *
     * @param bool $built
     *
     * @return static
     */
    public function setBuilt(bool $built = true): self
    {
        $this->built = $built;

        return $this;
    }
}