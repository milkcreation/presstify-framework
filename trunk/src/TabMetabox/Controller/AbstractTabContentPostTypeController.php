<?php

namespace tiFy\TabMetabox\Controller;

abstract class AbstractTabContentPostTypeController extends AbstractTabContentController
{
    /**
     * Récupération du type de post de l'environnement d'affichage de la page d'administration.
     *
     * @return string post|page|{custom_post_type}
     */
    public function getPostType()
    {
        return $this->getObjectName();
    }

    /**
     * Affichage.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des vaiables passés en argument.
     *
     * @return string
     */
    public function display($post, $args)
    {
        parent::display();
    }
}