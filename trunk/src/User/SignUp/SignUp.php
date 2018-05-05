<?php

/**
 * @name SignUp
 * @desc Interface d'inscription d'un nouvel utilisateur
 * @package presstiFy
 * @namespace tiFy\User\SignUp
 * @version 1.1
 * @subpackage Core
 * @since 1.2.571
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\User\SignUp;

use tiFy\Apps\AppController;
use tiFy\User\SignUp\SignUpController;

final class SignUp extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->appAddAction('init', null, 0);
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    public function init()
    {
        do_action('tify_user_signup_register', $this);
    }

    /**
     * Déclaration d'un formulaire d'inscription.
     *
     * @param string $name Identifiant de qualification du formulaire.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|SignupController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.user.signup.{$name}";

        if ($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new SignUpController($name, $attrs));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un formulaire d'inscription.
     *
     * @param string $name Identifiant de qualification.
     *
     * @return null|SignupController
     */
    public function get($name)
    {
        $alias = "tfy.user.signup.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }
}