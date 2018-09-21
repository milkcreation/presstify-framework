<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxDisplayUserInterface extends MetaboxDisplayInterface
{
    /**
     * Affichage.
     *
     * @param \WP_User $user Objet utilisateur Wordpress.
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($user, $args = []);
}