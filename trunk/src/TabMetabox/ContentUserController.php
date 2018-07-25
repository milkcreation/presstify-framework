<?php

namespace tiFy\TabMetabox;

use tiFy\TabMetabox\Controller\ContentController;

class ContentUserController extends ContentController
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