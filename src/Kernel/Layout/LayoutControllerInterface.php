<?php

namespace tiFy\Kernel\Layout;

use tiFy\Kernel\Layout\Db\DbControllerInterface;
use tiFy\Kernel\Layout\Labels\LabelsControllerInterface;
use tiFy\Kernel\Layout\Param\ParamCollectionInterface;
use tiFy\Kernel\Layout\Request\RequestInterface;
use tiFy\Apps\AppControllerInterface;

interface LayoutControllerInterface extends AppControllerInterface
{
    /**
     * Intialisation de la page d'affichage courant.
     *
     * @return void
     */
    public function current();

    /**
     * Récupération de la classe de rappel de l'object base de données
     *
     * @return null|DbControllerInterface
     */
    public function db();

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
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key);

    /**
     * Récupération de la classe de rappel du controleur des intitulés.
     *
     * @return LabelsControllerInterface
     */
    public function labels();

    /**
     * Récupération de la classe de rappel du controleur de message de notification.
     *
     * @return NoticeCollectionInterface
     */
    public function notices();

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
     * Préparation des éléments d'affichage.
     *
     * @return void
     */
    public function prepare();

    /**
     * Déclenchement de actions de traitement requises.
     *
     * @return void
     */
    public function process();

    /**
     * Récupération de la classe de rappel d'un controleur de service.
     *
     * @param string $key Clé d'indice de qualification du service.
     * @param null|array $args Listes des variables passées en argument.
     *
     * @return object
     */
    public function provide($key, $args = null);

    /**
     * Récupération de la classe de rappel du controleur de service.
     *
     * @return LayoutServiceProvider
     */
    public function provider();

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

    /**
     * Récupération de la classe de rappel du controleur de vue associé.
     *
     * @return string
     */
    public function view();
}