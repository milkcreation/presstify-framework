<?php

namespace tiFy\Contracts\Metabox;

use tiFy\Contracts\Kernel\ParametersBagInterface;
use tiFy\Contracts\Views\ViewInterface;
use tiFy\Contracts\Views\ViewsInterface;

interface MetaboxDisplayInterface extends ParametersBagInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Affichage du contenu.
     *
     * @param mixed $var1.
     * @param mixed $var2.
     * @param mixed $var3.
     *
     * @return string
     */
    public function content($var1 = null, $var2 = null, $var3 = null);

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
     * Affichage de l'entête.
     *
     * @param mixed $var1.
     * @param mixed $var2.
     * @param mixed $var3.
     *
     * @return string
     */
    public function header($var1 = null, $var2 = null, $var3 = null);

    /**
     * Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Instance du controleur d'écran de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($wp_screen);

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewsInterface|ViewInterface
     */
    public function viewer($view = null, $data = []);
}