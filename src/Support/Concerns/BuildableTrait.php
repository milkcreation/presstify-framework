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
}