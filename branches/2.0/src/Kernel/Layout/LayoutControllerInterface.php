<?php

namespace tiFy\Kernel\Layout;

use tiFy\Kernel\Layout\Param\ParamCollectionInterface;
use tiFy\Kernel\Layout\Request\RequestInterface;
use tiFy\Apps\AppControllerInterface;
use tiFy\Db\DbControllerInterface;

interface LayoutControllerInterface extends AppControllerInterface
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
     * Récupération du controleur de vue.
     *
     * @return string
     */
    public function getView();

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key);

    /**
     * Récupération de la classe de rappel du controleur de message de notification.
     *
     * @return NoticeCollectionInterface
     */
    public function notices();

    /**
     * Récupération de la classe de rappel d'un controleur.
     *
     * @return object
     */
    public function provide($key, $args = []);

    /**
     * Récupération de la classe de rappel du controleur de service.
     *
     * @return LayoutServiceProvider
     */
    public function provider();

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
     * @return ParamCollectionInterface
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
     * Récupération de la classe de rappel du controleur de requete Http.
     *
     * @return RequestInterface
     */
    public function request();

    /**
     * Affichage.
     *
     * @return string
     */
    public function render();
}