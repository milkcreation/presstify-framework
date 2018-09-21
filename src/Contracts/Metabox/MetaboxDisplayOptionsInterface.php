<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxDisplayOptionsInterface extends MetaboxDisplayInterface
{
    /**
     * Affichage.
     *
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($args = []);

    /**
     * Listes des options à enregistrer.
     *
     * @return array
     */
    public function settings();
}