<?php

namespace tiFy\Db;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\Db\Make;
use tiFy\Db\Handle;
use tiFy\Db\Meta;
use tiFy\Db\Parse;
use tiFy\Db\Query;
use tiFy\Db\Select;

class DbController extends AppController
{
    /**
     * Nom de qualification du controleur de base de données
     * @var string
     */
    protected $name = '';

    /**
     * Nom de qualification la table hors prefixe.
     * @var string
     */
    protected $tableShortName = '';

    /**
     * Nom réel de la table (prefixé)
     * @var string
     */
    protected $tableName = '';

    /**
     * Numéro de version.
     * @var int
     */
    protected $version = 0;

    /**
     * Préfixe des intitulés de colonne
     * @var string
     */
    protected $colPrefix = '';

    /**
     * Liste des noms de colonnes préfixés.
     * @var array
     */
    protected $colNames = [];

    /**
     * Cartographie des alias de colonnes.
     * @var array
     */
    protected $colMap = [];

    /**
     * Liste des attributs de configuration de colonne.
     * @var array
     */
    protected $colAttrs = [];

    /**
     * Nom de la colonne clé primaire.
     * @var null
     */
    protected $primary = null;

    /**
     * Liste des clés d'index.
     * @var array
     */
    protected $indexKeys = [];

    /**
     * Liste des noms de colonnes ouvertes à la recherche de termes.
     * @var string[]
     */
    protected $searchColumns = [];

    /**
     * Moteur de requête SQL.
     * @var null
     */
    protected $sqlEngine;

    /**
     * Identifiant de qualification la table d'enregistrement des metadonnées.
     * @var string
     */
    protected $metaType = '';

    /**
     * Nom de la colonne de jointure de la table d'enregistrement des metadonnées.
     * @var string
     */
    protected $metaJoinCol = '';

    /**
     * Variables de requête privées.
     * @var array
     */
    protected $privateQueryVars = [
        'include',
        /** @todo deprecated alias item__in * */
        'item__in',
        'exclude',
        /** @todo deprecated alias item__not_in * */
        'item__not_in',
        'search',
        /** @todo deprecated alias s * */
        's',
        'fields',
        'per_page',
        'paged',
        'order',
        'orderby',
        'item_meta',
        'limit',
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $id Nom de qualification du controleur de base de donnée
     * @param array $attrs  {
     *      Attributs de la table de base de données
     *
     *      @var bool $install Activation de l'installation de la table de base de données
     *      @var int $version Numéro de version de la table
     *      @var string $name Nom de la base de données (hors préfixe)
     *      @var string $primary Colonne de clé primaire
     *      @var string $col_prefix Prefixe des colonnes de la table
     *      @var array $columns {
     *          Liste des attributs de configuration des colonnes
     *
     *
     *      }
     *      @var array $keys {
     *          Liste des attributs de configuration des clefs d'index
     *
     *      }
     *      @var string[] $seach {
     *          Liste des colonnes ouvertes à la recherche
     *
     *      }
     *      @var bool|string|array $meta Activation ou nom de la table de stockage des metadonnées
     *      @var \wpdb|object $sql_engine Moteur (ORM) de requête en base de données
     * }
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        // Définition de l'identifiant de la table
        $this->name = $name;

        // Définition des attributs de la classe
        $defaults = [
            'install'    => false,
            'version'    => 1,
            'name'       => '',
            'primary'    => '',
            'col_prefix' => '',
            'columns'    => [],
            'keys'       => [],
            'search'     => [],
            'meta'       => false,
            // moteur de requete SQL global $wpdb par défaut | new \wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
            'sql_engine' => null,
        ];
        $attrs    = array_merge($defaults, $attrs);

        // Définition du numéro de version
        $this->version = $attrs['version'];

        // Définition du moteur de requête SQL
        $this->setSQLEngine($attrs['sql_engine']);

        // Définition du nom de la table en base de données
        $this->setTableName($attrs['name']);

        // Définition du préfixe par défaut des noms de colonnes
        $this->colPrefix = $attrs['col_prefix'];

        // Traitement des attributs de colonnes
        $this->setColumns($attrs['columns']);

        // Définition de la clé primaire
        $this->setPrimary($attrs['primary']);

        // Définition des clés d'index
        $this->indexKeys = $attrs['keys'];

        // Définition des colonnes ouvertes à la recherche de termes
        $this->setSearchColNames($attrs['search']);

        // Définition de nom de la table de metadonnées en base
        $this->setMeta($attrs['meta']);

        if ($attrs['install']) :
            $this->install();
        endif;
    }

    /**
     * Définition du moteur (ORM) de traitement des requête de base de données.
     *
     * @param \wpdb|object $sql_engine Moteur de traitement des requêtes de base.
     *
     * @return \wpdb|object
     */
    private function setSQLEngine($sql_engine = null)
    {
        if (is_null($sql_engine) || ! ($sql_engine instanceof \wpdb)) :
            global $wpdb;

            return $this->sqlEngine = $wpdb;
        endif;

        return $this->sqlEngine = $sql_engine;
    }

    /**
     * Définition du nom de la table en base de données.
     *
     * @param string $raw_name Nom de la table de base de données (hors prefixe).
     *
     * @return void
     */
    private function setTableName($raw_name = '')
    {
        if (! $raw_name) :
            $raw_name = $this->name;
        endif;

        $this->tableShortName = $raw_name;

        if (! in_array($raw_name, $this->sql()->tables)) :
            array_push($this->sql()->tables, $raw_name);
            $this->sql()->set_prefix($this->sql()->base_prefix);
        endif;

        $this->tableName = $this->sql()->{$raw_name};
    }

    /**
     * Définition des atttributs de configuration d'un colonne (prefixage + cartographie)
     *
     * @param array $columns Liste des colonnes.
     *
     * @return array
     */
    private function setColumns($columns)
    {
        foreach ($columns as $alias => $attrs) :
            $defaults = [
                'prefix' => true,
            ];
            $attrs = array_merge($defaults, $attrs);

            $name = $attrs['prefix'] ? $this->colPrefix . $alias : $alias;

            array_push($this->colNames, $name);

            $this->colMap[$alias] = $name;

            $this->colAttrs[$name] = $attrs;
        endforeach;
    }

    /**
     * Définition de la colonne utilisée en tant que clé primaire.
     *
     * @param string $primary Nom de la colonne de clé primaire.
     *
     * @return string
     */
    private function setPrimary($primary = '')
    {
        if (empty($this->colNames)) :
            return '';
        endif;

        $this->primary = ($primary && in_array($primary, $this->colNames))
            ? $primary
            : reset($this->colNames);
    }

    /**
     * Définition de la liste des colonnes ouverte à la recherche de terme.
     *
     * @param array $search_columns
     *
     * @return void
     */
    private function setSearchColNames($search_columns = [])
    {
        foreach ($search_columns as $alias) :
            if (isset($this->colMap[$alias])) :
                array_push($this->searchColumns, $this->colMap[$alias]);
            endif;
        endforeach;
    }

    /**
     * Définition des attributs de la table de gestion des métadonnées
     *
     * @return string
     */
    private function setMeta($meta_type = null)
    {
        if (! $meta_type) :
            return '';
        endif;

        if (is_string($meta_type)) :
        elseif (is_bool($meta_type)) :
            $meta_type = $this->tableShortName;
        elseif(is_array($meta_type)) :
            $this->metaJoinCol = Arr::get($meta_type, 'join_col', '');
            $meta_type = Arr::get($meta_type, 'meta_type', $this->tableShortName);
        endif;

        $table = $meta_type . 'meta';

        if (! in_array($table, $this->sql()->tables)) :
            array_push($this->sql()->tables, $table);
            $this->sql()->set_prefix($this->sql()->base_prefix);
        endif;

        $this->metaType = $meta_type;
    }

    /**
     * Récupération du nom de la table préfixée
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Récupération de la clé primaire
     *
     * @return string
     */
    final public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * Vérification si une colonne est la colonne déclarée comme primaire.
     *
     * @param string $name Identifiant de qualification de la colonne ou nom réel (préfixé) de la colonne.
     *
     * @return bool
     */
    final public function isPrimary($name)
    {
        if (! $name = $this->existsCol($name)) :
            return false;
        endif;

        return $this->getPrimary() === $name;
    }

    /**
     * Récupération du préfixe des colonnes de la table.
     *
     * @return string
     */
    public function getColPrefix()
    {
        return $this->colPrefix;
    }

    /**
     * Récupération de la liste des noms de colonnes réels (préfixés)
     *
     * @return string[]
     */
    public function getColNames()
    {
        return $this->colNames;
    }

    /**
     * Récupération du nom préfixé d'une colonne selon son alias de qualification.
     *
     * @param string $alias Alias de qualification d'une colonne.
     *
     * @return string
     */
    public function getColMap($alias)
    {
        if (isset($this->colMap[$alias])) :
            return $this->colMap[$alias];
        endif;

        return '';
    }

    /**
     * Récupération du nom réél (prefixé) d'une colonne.
     *
     * @param string $name Alias de qualification de la colonne ou nom réel (préfixé) de la colonne.
     *
     * @return string Nom de la colonne préfixée
     */
    public function existsCol($name)
    {
        if ($this->isPrivateQueryVar($name)) :
            return '';
        elseif (in_array($name, $this->getColNames())) :
            return $name;
        elseif ($name = $this->getColMap($name)) :
            return $name;
        endif;

        return '';
    }

    /**
     * Récupération des attributs de configuration d'une colonne.
     *
     * @param string $name Identifiant de qualification de la colonne ou nom réel (préfixé) de la colonne.
     *
     * @return array
     */
    public function getColAttrs($name)
    {
        if (! $name = $this->existsCol($name)) :
            return [];
        endif;

        if (isset($this->colAttrs[$name])) :
            return $this->colAttrs[$name];
        endif;

        return [];
    }

    /**
     * Récupération d'un attribut de configuration de colonne.
     *
     * @param string $name Identifiant de qualification de la colonne ou nom réel (préfixé) de la colonne.
     * @param string $key Index de qualification de l'attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getColAttr($name, $key, $default = '')
    {
        if(! $attrs =  $this->getColAttrs($name)) :
            return $default;
        endif;

        if (isset($attrs[$key])) :
            return $attrs[$key];
        endif;

        return $default;
    }

    /**
     * Vérification d'existance de gestion des metadonnée par le controleur.
     *
     * @return bool
     */
    public function hasMeta()
    {
        return ! empty($this->metaType);
    }

    /**
     * Récupération de l'identifiant de qualification la table d'enregistrement des metadonnées.
     *
     * @var string
     */
    public function getMetaType()
    {
        return $this->metaType;
    }

    /**
     * Récupération du nom de la colonne de jointure de la table d'enregistrement des metadonnées.
     *
     * @var string
     */
    public function getMetaJoinCol()
    {
        return $this->metaJoinCol;
    }

    /**
     * Récupération des clés d'index.
     *
     * @return array
     */
    public function getIndexKeys()
    {
        return $this->indexKeys;
    }

    /**
     * Vérifie si une variable de requête est une variable reservée par le système.
     *
     * @param string $var Variable de requête.
     *
     * @return bool
     */
    public function isPrivateQueryVar($var)
    {
        return in_array($var, $this->privateQueryVars);
    }

    /**
     * Vérification d'existance de colonnes ouvertes à la recherche de termes.
     *
     * @return bool
     */
    public function hasSearch()
    {
        return ! empty($this->searchColumns);
    }

    /**
     * Récupération de la liste des colonnes ouvertes à la recherche de termes.
     *
     * @return bool
     */
    public function getSearchColumns()
    {
        return ! empty($this->searchColumns);
    }

    /**
     * Moteur de requête SQL
     *
     * @return \wpdb
     */
    public function sql()
    {
        return $this->sqlEngine;
    }

    /**
     * Classe de rappel de traitement de l'installation des tables du controleur.
     *
     * @return void
     */
    public function install()
    {
        return (new Make($this))->install();
    }

    /**
     * Classe de rappel de traitement des données en base (création/édition/mise à jour ...).
     *
     * @return void
     */
    public function handle()
    {
        return new Handle($this);
    }

    /**
     * Classe de rappel de traitement des metadonnées en base (création/édition/mise à jour ...).
     *
     * @return void
     */
    public function meta()
    {
        return new Meta($this);
    }

    /**
     * Classe de rappel de traitement des arguments de requête.
     *
     * @return void
     */
    public function parse()
    {
        return new Parse($this);
    }

    /**
     * Traitement de la boucle de requêtes de récupération de données en base.
     *
     * @return void
     */
    public function query($query = null)
    {
        return new Query($this, $query);
    }

    /**
     * Traitement des requêtes de récupération de données en base.
     *
     * @return void
     */
    public function select($query = null)
    {
        return new Select($this);
    }
}