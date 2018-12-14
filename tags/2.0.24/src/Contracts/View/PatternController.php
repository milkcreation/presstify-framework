<?php

namespace tiFy\Contracts\View;

use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\Db\DbItemInterface;
use tiFy\Contracts\Kernel\LabelsBag;
use tiFy\Contracts\Kernel\Notices;
use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Contracts\Kernel\Request;

interface PatternController extends ContainerInterface
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
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération de l'instance du controleur de base de données
     *
     * @return null|DbItemInterface
     */
    public function db();

    /**
     * Récupération de l'instance de la fabrique de disposition associée.
     *
     * @return PatternFactory
     */
    public function factory();

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param array $args
     *
     * @return mixed.
     */
    public function get($id, array $args = []);

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
     * @param null|string $key Clé d'indice du paramètres. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return ParamsBag|mixed
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
     * Récupération de l'instance du controleur de gabarit d'affichage ou du gabarit qualifié.
     *
     * @param null|string $view Nom de qualification du ganarit d'affichage.
     * @param array $data Liste des variables passées en argument au gabarit.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = []);
}