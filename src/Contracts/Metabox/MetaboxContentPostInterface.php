<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxContentPostInterface extends MetaboxContentInterface
{
    /**
     * Affichage.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($post, $args = []);

    /**
     * Récupération du type de post de l'environnement d'affichage de la page d'administration.
     *
     * @return string post|page|{{custom_post_type}}
     */
    public function getPostType();

    /**
     * Listes des metadonnées à enregistrer.
     *
     * @return array
     */
    public function metadatas();
}