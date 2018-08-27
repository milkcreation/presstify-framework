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
use tiFy\App\AppController;

/**
 * @method static ActionLink ActionLink(string $id = null, array $attrs = [])
 * @method static AdminBar AdminBar(string $id = null,array $attrs = [])
 * @method static SwitcherForm SwitcherForm(string $id = null,array $attrs = [])
 */
class TakeOver extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->appAddAction('tify_partial_register');
    }

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

        $this->appAddAction('init');
    }

    /**
     * Déclaration de controleur d'affichage.
     *
     * @param Partial $partialController Classe de rappel des controleurs d'affichage.
     *
     * @return void
     */
    public function tify_partial_register($partialController)
    {
        $partialController->register(
            'TakeOverActionLink',
            ActionLink::class . "::make"
        );
        $partialController->register(
            'TakeOverAdminBar',
            AdminBar::class . "::make"
        );
        $partialController->register(
            'TakeOverSwitcherForm',
            SwitcherForm::class . "::make"
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
     * @param string $name Identifiant de qualification
     *
     * @return null|TakeOverController
     */
    public function get($name)
    {
        $alias = "tfy.user.take_over.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }

    /**
     * Affichage ou récupération du contenu d'un controleur natif.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     * @param array $args {
     *      Liste des attributs de configuration.
     *
     *      @var array $attrs Attributs de configuration du champ.
     *      @var bool $echo Activation de l'affichage du champ.
     *
     * @return null|callable
     */
    public static function __callStatic($name, $args)
    {
        return call_user_func_array(Partial::class . "::TakeOver{$name}", $args);
    }
}
