<?php

namespace tiFy\Components\AdminView\ListTable\BulkAction;

use Illuminate\Support\Arr;
use tiFy\AdminView\AdminViewInterface;
use tiFy\Partial\Partial;

class BulkActionItemController implements BulkActionItemInterface
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
        'value'   => '',
        'content' => '',
        'group'   => false,
        'attrs'   => [],
        'parent'  => ''
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
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
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
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

        if (!$this->get('content')) :
            $this->set('content', $this->name);
        endif;
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