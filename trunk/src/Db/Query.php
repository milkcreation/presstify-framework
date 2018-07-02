<?php

namespace tiFy\Db;

use tiFy\Apps\AppController;
use tiFy\Db\DbControllerInterface;

class Query
{
    /**
     * Classe de rappel du controleur de base de données associé.
     * @var DbControllerInterface
     */
    protected $db;

    /**
     * Variables de requête brutes, passées en arguments.
     * @var array
     */
    protected $query = [];

    /**
     * Variables de requête après traitement.
     * @var array
     */
    protected $query_vars = [];

    /**
     * Conservation de données de l'objet courant récupéré.
     * @var object
     */
    protected $queried_object;

    /**
     * Valeur de la clé primaire de l'objet récupéré
     * @var int|string
     */
    protected $queried_object_id;

    /**
     * Liste des éléments.
     * @var array
     */
    protected $items = [];

    /**
     * Nombre d'élément trouvés
     * @var int
     */
    protected $item_count = 0;

    /**
     * Indice de l'élément courant dans la boucle
     * @var int
     */
    protected $current_item = -1;

    /**
     * Indicateur d'activation de bouclage
     * @var bool
     */
    protected $in_the_loop = false;

    /**
     * Données de l'élément courant dans la boucle.
     * @var object
     */
    protected $item;

    /**
     * Nombre total d'élément correspondant à la requête
     * @var int
     */
    protected $found_items = 0;


    public $request;

    /**
     * CONSTRUCTEUR.
     *
     * @param DbControllerInterface $db Classe de rappel du controleur de base de données associé.
     *
     * @return void
     */
    public function __construct($query = null, DbControllerInterface $db)
    {
        $this->db = $db;

        if (!empty($query)) :
            $this->query($query);
        endif;
    }

    /**
     * Récupération du nombre d'éléments récupérés.
     *
     * @return array
     */
    public function getCount()
    {
        return $this->found_items;
    }

    /**
     * Récupération de l'attribut de l'élément dans la boucle.
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getField($key, $default = '')
    {
        if($key = $this->db->existsCol($key)) :
            return $this->item->{$key};
        endif;

        return $default;
    }

    /**
     * Récupération d'une metadonnée de l'élément dans la boucle.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée.
     * @param mixed $default Valeur de retour par défaut.
     * @param bool $single Indicateur de métadonnée simple ou multiple.
     *
     * @return mixed|void
     */
    public function getMeta($meta_key, $default = '', $single = true)
    {
        if (!$this->db->hasMeta()) :
            return $default;
        endif;

        return $this->db->meta()->get($this->item->{$this->db->getPrimary()}, $meta_key, $single);
    }

    /** ==    == **/
    public function get_adjacent($previous = true, $args = [])
    {
        $args = wp_parse_args($args, $this->query);
        return $this->db->select()->adjacent($this->item->{$this->db->getPrimary()}, $previous, $args);
    }

    /**
     * Récupération du nombre total d'éléments trouvés, correspondant aux variables de requête passé en argument.
     *
     * @return array
     */
    public function getFoundItems()
    {
        return $this->found_items;
    }

    /**
     * Récupération de la liste des éléments récupérés.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Vérifie l'existance d'éléments complémentaire dans la boucle.
     *
     * @return bool
     */
    public function haveItems()
    {
        if ($this->current_item + 1 < $this->item_count) :
            return true;
        elseif ($this->current_item + 1 == $this->item_count && $this->item_count > 0) :
            do_action_ref_array('tify_query_loop_end', [&$this]);
            $this->rewindItems();
        endif;

        $this->in_the_loop = false;

        return false;
    }

    /**
     * Définition du prochain élément dans boucle.
     *
     * @return object
     */
    public function nextItem()
    {
        $this->current_item++;

        $this->item = $this->items[$this->current_item];
        return $this->item;
    }

    /**
     * Traitement des variable de requête et récupération des éléments en base.
     *
     * @param array $query Liste des arguments de requêtes personnalisés.
     *
     * @return array
     */
    public function query($query = [])
    {
        $this->reset();
        $this->query = $this->query_vars = $query;

        return $this->queryItems();
    }

    /**
     * Récupération de la liste des éléments basé sur la configuration.
     *
     * @return array
     */
    public function queryItems()
    {
        if ($this->items = $this->db->select()->rows($this->query_vars)) :
            $this->item_count = count($this->items);
            $this->item = reset($this->items);
        else :
            $this->item_count = 0;
            $this->items = [];
        endif;

        $this->found_items = $this->db->select()->count($this->query_vars);

        return $this->items;
    }

    /**
     * Réinitialisation des propriétés et définition des valeurs par defaut.
     *
     * @return void
     */
    public function reset()
    {
        unset($this->items);
        unset($this->query);
        $this->query_vars = [];
        unset($this->queried_object);
        unset($this->queried_object_id);
        $this->item_count = 0;
        $this->current_item = -1;
        $this->in_the_loop = false;
        unset($this->request);
        unset($this->item);
        $this->found_items = 0;
    }

    /**
     * Réinitialisation de la boucle.
     *
     * @return void
     */
    public function rewindItems()
    {
        $this->current_item = -1;
        if ($this->item_count > 0) :
            $this->item = $this->items[0];
        endif;
    }

    /**
     * Définition de l'élément courant dans la boucle.
     *
     * @return void
     */
    public function theItem()
    {
        $this->in_the_loop = true;

        if ($this->current_item == -1) :
            do_action_ref_array('tify_db_query_loop_start', [&$this]);
        endif;

        $item = $this->nextItem();
    }

    /**
     * @todo
     */
    /** == Récupération des éléments à partir de conditions de requêtes == **/
    public function query_items($clauses = [])
    {
        extract($clauses);

        $where = isset($clauses['where']) ? $clauses['where'] : '';
        $groupby = isset($clauses['groupby']) ? $clauses['groupby'] : '';
        $join = isset($clauses['join']) ? $clauses['join'] : '';
        $orderby = isset($clauses['orderby']) ? $clauses['orderby'] : '';
        $distinct = isset($clauses['distinct']) ? $clauses['distinct'] : '';
        $fields = isset($clauses['fields']) ? $clauses['fields'] : "{$this->db->Name}.*";;
        $limits = isset($clauses['limits']) ? $clauses['limits'] : '';

        if (!empty($groupby)) {
            $groupby = 'GROUP BY ' . $groupby;
        }
        if (!empty($orderby)) {
            $orderby = 'ORDER BY ' . $orderby;
        }

        $found_rows = '';
        if (!empty($limits)) {
            $found_rows = 'SQL_CALC_FOUND_ROWS';
        }

        $this->request = "SELECT $found_rows $distinct $fields FROM {$this->db->Name} $join WHERE 1=1 $where $groupby $orderby $limits";

        if ($this->items = $this->db->sql()->get_results($this->request)) :
            $this->item_count = count($this->items);
            $this->item = reset($this->items);
        else :
            $this->item_count = 0;
            $this->items = [];
        endif;

        $this->set_found_items($limits);

        return $this->items;
    }

    private function set_found_items($limits)
    {
        if (is_array($this->items) && !$this->items) {
            return;
        }

        if (!empty($limits)) :
            $this->found_items = $this->db->sql()->get_var('SELECT FOUND_ROWS()');
        else :
            $this->found_items = count($this->items);
        endif;

        if (!empty($limits)) {
            $this->max_num_pages = ceil($this->found_items / 10);
        }
    }
}