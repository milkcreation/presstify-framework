<?php

namespace tiFy\TabMetabox\Controller;

interface ContentInterface
{
    /**
     * Pré-Chargement de la page d'administration courante de Wordpress. Déclaration de l'écran courant.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function _load($wp_screen);

    /**
     * Récupération de la liste des attributs.
     *
     * @return array
     */
    public function all();

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Définition de la liste des attributs par défaut.
     *
     * @return array
     */
    public function defaults();

    /**
     * Récupération d'un attribut.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut lorsque l'attribut n'est pas défini.
     *
     * @return mixed
     */
    public function get($key, $default = '');

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
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key);

    /**
     * Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($wp_screen);

    /**
     * Traitement de la liste des attributs.
     *
     * @param array $attrs Liste des attribut à traiter.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Définition d'un attribut.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return void
     */
    public function set($key, $value);
}