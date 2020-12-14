<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface Modal extends PartialDriver
{
    /**
     * Affichage d'un lien de déclenchement de la modale.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return string
     */
    public function trigger(array $attrs = []): string;
}