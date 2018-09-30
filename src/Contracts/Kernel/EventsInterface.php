<?php

namespace tiFy\Contracts\Kernel;

use League\Event\EmitterInterface;
use League\Event\EventInterface;

interface EventsInterface extends EmitterInterface
{
    /**
     * Déclaration d'un événement.
     * @see http://event.thephpleague.com/2.0/emitter/basic-usage/
     *
     * @param string $name Identifiant de qualification de l'événement.
     * @param callable|ListenerInterface $listener Fonction anonyme ou Classe de traitement de l'événement.
     * @param int $priority Priorité de traitement.
     *
     * @return $this
     */
    public function listen($name, $listener, $priority = 0);

    /**
     * Déclenchement d'un événement.
     * @see http://event.thephpleague.com/2.0/events/classes/
     *
     * @param string|object $event Identifiant de qualification de l'événement.
     * @param mixed ... $args Variable(s) passée(s) en argument.
     *
     * @return null|EventInterface
     */
    public function trigger($event);
}