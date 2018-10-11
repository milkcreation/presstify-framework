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

use tiFy\User\SignUp\SignUpItemController;

final class SignUp
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function() {
                do_action('tify_user_signup_register', $this);
            },
            0
        );
    }

    /**
     * Déclaration d'un formulaire d'inscription.
     *
     * @param string $name Identifiant de qualification du formulaire.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|SignUpItemController
     */
    public function register($name, $attrs = [])
    {
        $alias = "user.signup.{$name}";
        if (app()->has($alias)) :
            return;
        endif;

        $attrs = array_merge(['controller' => SignUpItemController::class], $attrs);
        $controller = $attrs['controller'];

        try {
            $resolved = new $controller($name, $attrs, $this);
            app()->singleton(
                $alias,
                function () use ($resolved) {
                    return $resolved;
                }
            );
        } catch(\InvalidArgumentException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
        }

        return $resolved;
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
        $alias = "user.signup.{$name}";

        if (app()->has($alias)) :
            return app()->resolve($alias);
        else :
            return null;
        endif;
    }
}