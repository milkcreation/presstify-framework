<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxContentUserInterface extends MetaboxContentInterface
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