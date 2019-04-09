<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Db\DbFactory;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\View\ViewEngine;

interface TemplateFactory
{
    /**
     * Résolution de sortie de la classe en tant qu'instance.
     *
     * @param string $name Nom de qualification.
     *
     * @return static
     */
    public function __invoke(string $name): TemplateFactory;

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Instance du controleur de gestion des assets.
     *
     * @return FactoryAssets
     */
    public function assets(): FactoryAssets;

    /**
     * Initialisation du controleur.
     *
     * @return static
     */
    public function boot(): TemplateFactory;

    /**
     * Vérification d'existance d'un service fourni.
     *
     * @param string $alias Alias de qualification du service.
     *
     * @return mixed.
     */
    public function bound(string $alias);

    /**
     * Récupération de l'instance de gestion de la configuration ou Définition d'attributs de configuration ou
     * récupération d'un attribut de configuration.
     *
     * @param null|string|array $key Clé d'indice de l'attribut de configuration. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return ParamsBag|mixed
     */
    public function config($key = null, $default = null);

    /**
     * Récupération de l'instance du controleur de base de données
     *
     * @return FactoryDb|DbFactory|null
     */
    public function db();

    /**
     * Affichage du rendu.
     *
     * @return void
     */
    public function display();

    /**
     * Récupération du conteneur d'injection de dépendances.
     *
     * @return Container
     */
    public function getContainer(): Container;

    /**
     * Récupération de la liste des fournisseurs de services.
     *
     * @return string[]
     */
    public function getServiceProviders();

    /**
     * Récupération de l'instance du controleur des intitulés ou récupération d'un intitulé.
     *
     * @param string|null $key Clé d'indexe de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return FactoryLabels|string
     */
    public function label(?string $key = null, string $default = '');

    /**
     * Intialisation de l'affichage de la disposition.
     *
     * @return static
     */
    public function load(): TemplateFactory;

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Récupération de l'instance du controleur de message de notification.
     *
     * @return FactoryNotices
     */
    public function notices(): FactoryNotices;

    /**
     * Récupération de l'instance du controleur de paramètre ou récupération d'un paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètres. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return FactoryParams|mixed
     */
    public function param($key = null, $default = null);

    /**
     * Préparation des éléments d'affichage.
     *
     * @return static
     */
    public function prepare(): TemplateFactory;

    /**
     * Déclenchement de actions de traitement requises.
     *
     * @return static
     */
    public function process(): TemplateFactory;

    /**
     * Récupération de l'instance du controleur de requête Http.
     *
     * @return FactoryRequest
     */
    public function request(): FactoryRequest;

    /**
     * Affichage.
     *
     * @return string
     */
    public function render();

    /**
     * Récupération d'une instance de service fourni.
     *
     * @param string $alias Nom de qualification du service.
     * @param array $args Liste des variables passées en argument
     *
     * @return mixed.
     */
    public function resolve(string $alias, array $args = []);

    /**
     * Récupération de l'identifiant de qualification compatible à l'utilisation dans une url.
     *
     * @return string
     */
    public function slug(): string;

    /**
     * Instance du controleur de gestion des urls.
     *
     * @return FactoryUrl
     */
    public function url(): FactoryUrl;

    /**
     * Récupération de l'instance du controleur de gabarit d'affichage ou du gabarit qualifié.
     *
     * @param string|null $view Nom de qualification du gabarit d'affichage.
     * @param array $data Liste des variables passées en argument au gabarit.
     *
     * @return FactoryViewer|ViewEngine
     */
    public function viewer(?string $view = null, array $data = []);
}