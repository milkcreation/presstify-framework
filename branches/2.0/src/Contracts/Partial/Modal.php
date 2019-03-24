<?php

namespace tiFy\Contracts\Partial;

interface Modal extends PartialFactory
{
    /**
     * Affichage d'un lien de déclenchement de la modale.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return string
     */
    public function trigger($attrs = []);

    /**
     * Chargement du contenu de la modale via Ajax.
     *
     * @return void
     */
    public function wp_ajax();
}