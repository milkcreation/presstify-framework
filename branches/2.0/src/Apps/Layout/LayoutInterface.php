<?php

namespace tiFy\Apps\Layout;

use tiFy\Apps\Layout\Db\DbInterface;
use tiFy\Apps\Layout\Labels\LabelsInterface;
use tiFy\Apps\Layout\Notices\NoticesInterface;
use tiFy\Apps\Layout\Params\ParamsInterface;
use tiFy\Apps\Layout\Request\RequestInterface;
use tiFy\Apps\Container\ContainerInterface;

interface LayoutInterface extends ContainerInterface
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
     * @return null|DbInterface
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
     * @return LabelsInterface
     */
    public function labels();

    /**
     * Récupération de la classe de rappel du controleur de message de notification.
     *
     * @return NoticesInterface
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
     * @return ParamsInterface
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