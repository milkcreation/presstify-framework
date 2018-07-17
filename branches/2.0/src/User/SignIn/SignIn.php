<?php

/**
 * @name SignIn
 * @desc Interface d'authentification utilisateur.
 * @package presstiFy
 * @namespace tiFy\User\SignIn
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\User\SignIn;

use tiFy\Apps\AppController;
use tiFy\User\User;
use tiFy\User\SignIn\SignInControllerInterface;
use tiFy\User\SignIn\SignInItemController;

final class SignIn extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
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
        if ($signins = $this->appConfig('signin', [], User::class)) :
            foreach ($signins as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;
        endif;

        // Déclaration des interfaces d'authentification ponctuelles
        do_action('tify_user_signin_register', $this);
    }

    /**
     * Déclaration d'un formulaire d'authentification.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @return SignInControllerInterface
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.user.signin.{$name}";
        if ($this->appServiceHas($alias)) :
            return;
        endif;

        $attrs = array_merge(['controller' => SignInItemController::class], $attrs);
        $controller = $attrs['controller'];

        try {
            $concrete = new $controller($name, $attrs, $this);
            $this->appServiceShare($alias, $concrete);
        } catch(\InvalidArgumentException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
        }

        return $concrete;
    }

    /**
     * Récupération d'une classe de rappel de formulaire d'authentification déclaré.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     *
     * @return null|SignInControllerInterface
     */
    public function get($name)
    {
        $alias = "tfy.user.signin.{$name}";

        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;

        return null;
    }

    /**
     * Affichage d'un élément de gabarit.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     * @param string $template Méthode d'affichage de SignInControllerInterface.
     * @param array $attrs Attribut d'affichage du gabarit.
     *
     * @return string
     */
    public function display($name, $template, $attrs = [], $echo = true)
    {
        if (!$instance = $this->get($name)) :
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