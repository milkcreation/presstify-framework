<?php

namespace tiFy\Contracts\Api;

use League\Event\Event;

interface FacebookItemInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Traitement.
     *
     * @param Event $event Instance de l'événement.
     * @param string $action Nom de qualification de l'action.
     *
     * @return string
     */
    public function process(Event $event, $action = '');

    /**
     * Url de l'action.
     *
     * @param string $action Nom de qualification de l'action.
     * @param array $permissions Liste des permissions accordées (scope).
     * @param string $redirect_url Url de retour.
     *
     * @return string
     */
    public function url($action = '', $permissions = ['email'], $redirect_url = '');

    /**
     * Bouton de lancement de l'action.
     *
     * @param string $action Nom de qualification de l'action.
     * @param array $attrs Liste des attributs de configuration
     *
     * @return string
     */
    public function trigger($action = '', $attrs = []);
}