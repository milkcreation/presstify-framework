<?php

declare(strict_types=1);

namespace tiFy\Contracts\Form;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface EventsFactory
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): EventsFactory;

    /**
     * Déclaration d'un événement.
     *
     * @param string $name Identifiant de qualification de l'événement.
     * @param callable $listener Fonction anonyme ou Classe de traitement de l'événement.
     * @param int $priority Priorité de traitement.
     *
     * @return static
     */
    public function on(string $name, callable $listener, int $priority = 0): EventsFactory;

    /**
     * Déclenchement d'un événement.
     *
     * @param string $name Nom de qualification de l'événement.
     * @param array $args Variable passées en argument à la fonction d'écoute.
     *
     * @return void
     */
    public function trigger(string $name, array $args = []): void;
}