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

use tiFy\User\User;
use tiFy\Contracts\User\UserSignInItemInterface;
use tiFy\User\SignIn\SignInItemController;

final class SignIn
{
    /**
     * Liste des éléments déclarés.
     * @var UserSignInItemInterface
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                if ($signins = config('user.signin', [])) :
                    foreach ($signins as $name => $attrs) :
                        $this->_register($name, $attrs);
                    endforeach;
                endif;
            }
        );
    }

    /**
     * Enregistement de la déclaration d'un formulaire d'authentification.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return UserSignInItemInterface
     */
    public function _register($name, $attrs = [])
    {
        if (isset($this->items[$name])) :
            return $this->items[$name];
        endif;

        $attrs = array_merge(['controller' => SignInItemController::class], $attrs);
        $controller = $attrs['controller'];

        try {
            $resolved = new $controller($name, $attrs, $this);
            app()->singleton(
                "user.signin.{$name}",
                function () use ($resolved) {
                    return $resolved;
                }
            );
        } catch(\InvalidArgumentException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
        }

        return $this->items[$name] = $resolved;
    }

    /**
     * Déclaration d'un formulaire d'authentification.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs = [])
    {
        config()->set(
            'user.signin',
            array_merge(
                [$name => $attrs],
                config('user.signin', [])
            )
        );

        return $this;
    }

    /**
     * Récupération d'une classe de rappel de formulaire d'authentification déclaré.
     *
     * @param string $name Nom de qualification du formulaire d'authentification.
     *
     * @return null|UserSignInItemInterface
     */
    public function get($name)
    {
        if (isset($this->items[$name])) :
            return $this->items[$name];
        else :
            return null;
        endif;
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