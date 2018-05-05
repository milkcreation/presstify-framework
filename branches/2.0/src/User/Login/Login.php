<?php

/**
 * @name Login
 * @desc Interface d'authentification utilisateur
 * @package presstiFy
 * @namespace tiFy\User\Login
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\User\Login;

use tiFy\Apps\AppController;
use tiFy\User\User;
use tiFy\User\Login\LoginController;

final class Login extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->appAddAction('init');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    final public function init()
    {
        // Déclaration des interfaces d'authentification configurées
        if ($logins = $this->appConfig('login', [], User::class)) :
            foreach ($logins as $id => $attrs) :
                $this->register($id, $attrs);
            endforeach;
        endif;

        // Déclaration des interfaces d'authentification ponctuelles
        do_action('tify_user_login_register', $this);
    }

    /**
     * Déclaration d'un formulaire d'authentification.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     * @param array $attrs Attributs de configuration.
     *
     * @return LoginController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.user.login.{$name}";

        if ($this->appServiceHas($alias)) :
            return;
        endif;

        $defaults = [
            'controller'    => LoginController::class
        ];
        $attrs = array_merge($defaults, $attrs);
        $classname  = $attrs['controller'];

        $this->appServiceShare($alias, new $classname($id, $attrs));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'une classe de rappel de formulaire d'authentification déclaré.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     *
     * @return null|LoginController
     */
    public function get($name)
    {
        $alias = "tfy.user.login.{$name}";

        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;

        return null;
    }

    /**
     * Affichage d'un élément de gabarit.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     * @param string $template Méthode de la classe \tiFy\User\Login\Factory d'affichage.
     * @param array $attrs Attribut d'affichage du gabarit.
     *
     * @return string
     */
    public function display($name, $template, $attrs = [], $echo = true)
    {
        if (! $instance = $this->get($name)) :
            return '';
        endif;

        $output = call_user_func_array([$instance, 'display'], [$template, $attrs, $echo]);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
}