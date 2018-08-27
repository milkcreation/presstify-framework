<?php

namespace tiFy\Db;

use tiFy\Db\Make;
use tiFy\Db\Handle;
use tiFy\Db\Meta;
use tiFy\Db\Parse;
use tiFy\Db\Query;
use tiFy\Db\Select;

interface DbControllerInterface
{
    /**
     * Récupération du nom réél (prefixé) d'une colonne.
     *
     * @param string $name Alias de qualification de la colonne ou nom réel (préfixé) de la colonne.
     *
     * @return string Nom de la colonne préfixée
     */
    public function existsCol($name);

    /**
     * Récupération d'un attribut de configuration de colonne.
     *
     * @param string $name Identifiant de qualification de la colonne ou nom réel (préfixé) de la colonne.
     * @param string $key Index de qualification de l'attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getColAttr($name, $key, $default = '');

    /**
     * Récupération des attributs de configuration d'une colonne.
     *
     * @param string $name Identifiant de qualification de la colonne ou nom réel (préfixé) de la colonne.
     *
     * @return array
     */
    public function getColAttrs($name);

    /**
     * Récupération du nom préfixé d'une colonne selon son alias de qualification.
     *
     * @param string $alias Alias de qualification d'une colonne.
     *
     * @return string
     */
    public function getColMap($alias);
    /**
     * Récupération de la liste des noms de colonnes réels (préfixés)
     *
     * @return string[]
     */
    public function getColNames();

    /**
     * Récupération du préfixe des colonnes de la table.
     *
     * @return string
     */
    public function getColPrefix();

    /**
     * Récupération des clés d'index.
     *
     * @return array
     */
    public function getIndexKeys();

    /**
     * Récupération du nom de la colonne de jointure de la table d'enregistrement des metadonnées.
     *
     * @var string
     */
    public function getMetaJoinCol();

    /**
     * Récupération de l'identifiant de qualification la table d'enregistrement des metadonnées.
     *
     * @var string
     */
    public function getMetaType();

    /**
     * Récupération de la clé primaire
     *
     * @return string
     */
    public function getPrimary();

    /**
     * Récupération de la liste des colonnes ouvertes à la recherche de termes.
     *
     * @return bool
     */
    public function getSearchColumns();

    /**
     * Récupération du nom de la table préfixée
     *
     * @return string
     */
    public function getTableName();

    /**
     * Classe de rappel de traitement des données en base (création/édition/mise à jour ...).
     *
     * @return Handle
     */
    public function handle();

    /**
     * Vérification d'existance de gestion des metadonnée par le controleur.
     *
     * @return bool
     */
    public function hasMeta();

    /**
     * Vérification d'existance de colonnes ouvertes à la recherche de termes.
     *
     * @return bool
     */
    public function hasSearch();

    /**
     * Classe de rappel de traitement de l'installation des tables du controleur.
     *
     * @return Make
     */
    public function install();

    /**
     * Vérification si une colonne est la colonne déclarée comme primaire.
     *
     * @param string $name Identifiant de qualification de la colonne ou nom réel (préfixé) de la colonne.
     *
     * @return bool
     */
    public function isPrimary($name);

    /**
     * Vérifie si une variable de requête est une variable reservée par le système.
     *
     * @param string $var Variable de requête.
     *
     * @return bool
     */
    public function isPrivateQueryVar($var);

    /**
     * Classe de rappel de traitement des metadonnées en base (création/édition/mise à jour ...).
     *
     * @return Meta
     */
    public function meta();

    /**
     * Classe de rappel de traitement des arguments de requête.
     *
     * @return Parse
     */
    public function parser();

    /**
     * Traitement de la boucle de requêtes de récupération de données en base.
     *
     * @return Query
     */
    public function query($query = null);

    /**
     * Traitement des requêtes de récupération de données en base.
     *
     * @return Select
     */
    public function select($query = null);

    /**
     * Moteur de requête SQL
     *
     * @return \wpdb
     */
    public function sql();
}