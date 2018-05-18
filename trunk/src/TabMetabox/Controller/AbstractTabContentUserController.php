<?php

namespace tiFy\TabMetabox\Controller;

abstract class AbstractTabContentUserController extends AbstractTabContentController
{
    /**
     * Affichage.
     *
     * @param \WP_User $user Objet utilisateur Wordpress.
     * @param array $args Liste des vaiables passés en argument.
     *
     * @return string
     */
    public function display($user, $args)
    {
        parent::display();
    }
}