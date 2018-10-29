<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxWpPostController extends MetaboxController
{
    /**
     * Affichage du contenu.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des variables passés en argument.
     * @param null $null Paramètre indisponible.
     *
     * @return string
     */
    public function content($post = null, $args = null, $null = null);

    /**
     * Récupération du type de post de l'environnement d'affichage de la page d'administration.
     *
     * @return string post|page|{{custom_post_type}}
     */
    public function getPostType();

    /**
     * Affichage de l'entête.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des variables passés en argument.
     * @param null $null Paramètre indisponible.
     *
     * @return string
     */
    public function header($post = null, $args = null, $null = null);

    /**
     * Listes des metadonnées à enregistrer.
     *
     * @return array
     */
    public function metadatas();
}