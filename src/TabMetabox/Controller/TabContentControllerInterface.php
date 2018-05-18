<?php

namespace tiFy\TabMetabox\Controller;

interface TabContentControllerInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Pré-Chargement de la page d'administration courante de Wordpress. Déclaration de l'écran courant.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function _load($wp_screen);

    /**
     * Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($wp_screen);

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
}