<?php

namespace tiFy\TabMetabox\Controller;

use Illuminate\Support\Arr;
use tiFy\App\AppController;

class AbstractTabItemController extends AppController
{
    /**
     * Compteur d'instance
     * @var int
     */
    protected static $instance = 0;

    /**
     * Indice de l'élément.
     * @return int
     */
    protected $index = 0;

    /**
     * Alias de qualification de l'accroche de la page d'administration de l'élément.
     * @var string
     */
    protected $alias = '';

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * Nom de qualification de l'environnement d'affichage de la page d'administration.
     * @var string Nom de la page d'option|Nom du type de post|Nom de la taxonomie|Nom du rôle
     */
    protected $object_name = '';

    /**
     * Environnement d'affichage de la page d'administration.
     * @var string options|post_type|taxonomy|user
     */
    protected $object_type = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $alias Alias de qualification de l'accroche de la page d'administration de l'élément.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($object_name, $object_type, $alias, $attrs = [])
    {
        parent::__construct();

        $this->object_name = $object_name;
        $this->object_type = $object_type;
        $this->alias = $alias;
        $this->index = self::$instance++;

        $this->parse($attrs);
    }

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attributs à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération du nom de qualification de l'élément.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Récupération de l'indice de l'élément.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Récupération du nom de qualification de l'élément.
     *
     * @return int
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * Traitement de la liste des attributs de configuration.
     *
     * @return void
     */
    protected function parse($attrs = [])
    {
        $this->attributes = $attrs;
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attributs à définir. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }
}