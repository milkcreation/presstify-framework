<?php

namespace tiFy\Components\AdminView\ListTable\Column;

use ArrayIterator;
use Illuminate\Support\Arr;
use tiFy\AdminView\AdminViewInterface;

class ColumnItemController implements ColumnItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewInterface
     */
    protected $view;

    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'content'  => '',
        'name'     => '',
        'title'    => '',
        'sortable' => false,
        'hidden'   => false,
        'primary'  => false
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param array|object $item Données de l'élément courant.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AdminViewInterface $view)
    {
        $this->name = $name;
        $this->view = $view;

        $this->parse($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        if (!$value = $item->get($this->name)) :
            return;
        endif;

        $type = (($db = $this->view->getDb()) && $db->existsCol($this->name)) ? strtoupper($db->getColAttr($this->name,
            'type')) : '';

        switch ($type) :
            default:
                if (is_array($value)) :
                    return join(', ', $value);
                else :
                    return $value;
                endif;
                break;
            case 'DATETIME' :
                return \mysql2date(get_option('date_format') . ' @ ' . get_option('time_format'), $value);
                break;
        endswitch;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * Vérifie l'existance d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Récupération de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Définition de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     * @param mixed $value Valeur à définir.
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) :
            $this->attributes[] = $value;
        else :
            $this->attributes[$key] = $value;
        endif;
    }

    /**
     * Suppression de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        if ($sortable = $this->get('sortable')) :
            $this->set('sortable', is_bool($sortable) ? $this->name : $sortable);
        endif;

        $this->set('name', $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }
}