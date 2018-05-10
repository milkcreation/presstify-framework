<?php

/**
 * @name TakeOver
 * @desc Prise de controle de compte utilisateur
 * @package presstiFy
 * @namespace tiFy\User\TakeOver
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\User\TakeOver;

use tiFy\Partial\Partial;
use tiFy\User\User;
use tiFy\User\TakeOver\ActionLink\ActionLink;
use tiFy\User\TakeOver\AdminBar\AdminBar;
use tiFy\User\TakeOver\SwitcherForm\SwitcherForm;
use tiFy\Apps\AppController;

class TakeOver extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        // Activation des permissions de prises de contrôle de comptes utilisateurs
        if ($take_over = $this->appConfig('take_over', [], User::class)) :
            foreach ($take_over as $id => $attrs) :
                $this->register($id, $attrs);
            endforeach;
        endif;

        // Déclaration des événements de déclenchement
        $this->appAddAction('tify_partial_register');
        $this->appAddAction('init');
    }

    /**
     * Déclaration de controleur d'affichage.
     *
     * @param Partial $partial Classe de rappel des controleurs d'affichage.
     *
     * @return void
     */
    public function tify_partial_register($partial)
    {
        $partial->register(
            'TakeOverActionLink',
            ActionLink::class
        );
        $partial->register(
            'TakeOverAdminBar',
            AdminBar::class
        );
        $partial->register(
            'TakeOverSwitcherForm',
            SwitcherForm::class
        );
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    final public function init()
    {
        do_action('tify_user_take_over_register', $this);
    }

    /**
     * Déclaration des classes de rappel de prise de contrôle de compte utilisateur
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|TakeOverController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.user.take_over.{$name}";
        if ($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new TakeOverController($name, $attrs));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération des classes de rappel de prise de contrôle de compte utilisateur
     *
     * @param string $id Identifiant de qualification
     *
     * @return null|TakeOverController
     */
    public function get($id)
    {
        $alias = "tfy.user.take_over.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }
}
