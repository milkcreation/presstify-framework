<?php

namespace tiFy\AdminView;

use tiFy\AdminView\AdminViewParamsControllerInterface;
use tiFy\Apps\AppControllerInterface;
use tiFy\Db\DbControllerInterface;

interface AdminViewInterface extends AppControllerInterface
{
    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Récupération de la classe de controle d'un service.
     *
     * @param string $key Clé d'indexe du fournisseur de service.
     * @param string $default Valeur de retour par défaut.
     *
     * @return callable
     */
    public function getConcrete($key, $default = null);

    /**
     * Récupération de la classe de rappel de l'object base de données
     *
     * @return null|DbControllerInterface
     */
    public function getDb();

    /**
     * Récupération du nom de qualification d'accroche de la page d'affichage de l'interface.
     *
     * @return string
     */
    public function getHookname();

    /**
     * Récupération d'un intitulé.
     *
     * @param string $key Clé d'indexe de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getLabel($key, $default = '');

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getScreen();

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key);

    /**
     * Récupération d'un paramètre.
     *
     * @param string $key Clé d'indice du paramètres.Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function param($key, $default = null);

    /**
     * Récupération de la classe de rappel du controleur de gestion des paramètres.
     *
     * @return AdminViewParamsControllerInterface
     */
    public function params();

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Affichage.
     *
     * @return string
     */
    public function render();
}