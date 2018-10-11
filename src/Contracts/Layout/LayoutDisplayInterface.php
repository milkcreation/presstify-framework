<?php

namespace tiFy\Contracts\Layout;

use tiFy\Contracts\Container\ContainerInterface;

interface LayoutDisplayInterface extends ContainerInterface
{
    /**
     * Résolution de sortie de la classe en tant qu'instance.
     *
     * @return string
     */
    public function __invoke();

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération de l'instance du controleur de base de données
     *
     * @return null|LayoutDisplayDbInterface
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
     * Récupération de l'instance de la fabrique de disposition associée.
     *
     * @return LayoutFactoryInterface
     */
    public function factory();

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key);

    /**
     * Récupération de l'instance du controleur des intitulés ou récupération d'un intitulé.
     *
     * @param null|string $key Clé d'indexe de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return LayoutDisplayLabelsInterface|string
     */
    public function label($key, $default = '');

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
     * @return LayoutDisplayNoticesInterface
     */
    public function notices();

    /**
     * Récupération de l'instance du controleur de paramètre ou récupération d'un paramètre.
     *
     * @param null|string $key Clé d'indice du paramètres. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return LayoutDisplayParamsInterface|mixed
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
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Récupération de l'instance du controleur de requête Http.
     *
     * @return LayoutDisplayRequestInterface
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
    public function viewer($view = null, $data = []);
}