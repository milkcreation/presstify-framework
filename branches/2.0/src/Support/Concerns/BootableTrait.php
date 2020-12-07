<?php declare(strict_types=1);

namespace tiFy\Support\Concerns;

trait BootableTrait
{
    /**
     * Etat de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Vérification de l'état de chargement.
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }
}