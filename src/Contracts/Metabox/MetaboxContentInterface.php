<?php

namespace tiFy\Contracts\Metabox;

use tiFy\Contracts\Kernel\ParametersBagInterface;

interface MetaboxContentInterface extends ParametersBagInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération du nom de qualification de l'environnement d'affichage de la page d'administration.
     *
     * @return string
     */
    public function getObjectName();

    /**
     * Récupération de l'environnement d'affichage de la page d'administration.
     *
     * @return string options|post_type|taxonomy|user
     */
    public function getObjectType();

    /**
     * Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Instance du controleur d'écran de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($wp_screen);
}