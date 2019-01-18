<?php

namespace tiFy\Contracts\Template;

use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\Db\DbItemInterface;
use tiFy\Contracts\Kernel\LabelsBag;
use tiFy\Contracts\Kernel\Notices;
use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Contracts\Kernel\Request;
use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Template\Templates\BaseUrl;
use League\Container\Definition\DefinitionInterface;

interface TemplateFactory
{
    /**
     * Résolution de sortie de la classe en tant qu'instance.
     *
     * @param string $name Nom de qualification.
     *
     * @return string
     */
    public function __invoke($name);

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Instance du controleur de gestion des assets.
     *
     * @return object
     */
    public function assets();

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Vérification d'existance d'un service fourni.
     *
     * @param string $alias Alias de qualification du service.
     *
     * @return mixed.
     */
    public function bound($alias);

    /**
     * Récupération de l'instance de gestion de la configuration ou Définition d'attributs de configuration ou
     * récupération d'un attribut de configuration.
     *
     * @param null|string|array $key Clé d'indice de l'attribut de configuration. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return self|ParamsBag|mixed
     */
    public function config($key, $default = null);

    /**
     * Récupération de l'instance du controleur de base de données
     *
     * @return null|DbItemInterface
     */
    public function db();

    /**
     * Affichage du rendu.
     *
     * @return void
     */
    public function display();

    /**
     * Récupération de l'instance d'un service fournis en vue de sa redéfinition.
     *
     * @param string $alias Alias de qualification du service.
     *
     * @return DefinitionInterface
     */
    public function extend($alias);

    /**
     * Récupération du conteneur d'injection de dépendances.
     *
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * Récupération de la liste des fournisseurs de services.
     *
     * @return string[]
     */
    public function getServiceProviders();

    /**
     * Récupération de l'instance du controleur des intitulés ou récupération d'un intitulé.
     *
     * @param null|string $key Clé d'indexe de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return LabelsBag|string
     */
    public function label($key = null, $default = '');

    /**
     * Intialisation de l'affichage de la disposition.
     *
     * @return void
     */
    public function load();

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function name();

    /**
     * Récupération de l'instance du controleur de message de notification.
     *
     * @return Notices
     */
    public function notices();

    /**
     * Récupération de l'instance du controleur de paramètre ou récupération d'un paramètre.
     *
     * @param null|array|string $key Clé d'indice du paramètres. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return self|ParamsBag|mixed
     */
    public function param($key = null, $default = null);

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
     * Récupération de l'instance du controleur de requête Http.
     *
     * @return Request
     */
    public function request();

    /**
     * Affichage.
     *
     * @return string
     */
    public function render();

    /**
     * Récupération d'une instance de service fourni.
     *
     * @param string $id
     * @param array $args
     *
     * @return mixed.
     */
    public function resolve($id, array $args = []);

    /**
     * Instance du controleur de gestion des urls.
     *
     * @return BaseUrl
     */
    public function url();

    /**
     * Récupération de l'instance du controleur de gabarit d'affichage ou du gabarit qualifié.
     *
     * @param null|string $view Nom de qualification du ganarit d'affichage.
     * @param array $data Liste des variables passées en argument au gabarit.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = []);
}