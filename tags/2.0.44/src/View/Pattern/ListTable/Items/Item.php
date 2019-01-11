<?php

namespace tiFy\View\Pattern\ListTable\Items;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\View\Pattern\ListTable\Contracts\Item as ItemContract;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class Item extends ParamsBag implements ItemContract
{
    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param mixed $attrs Liste des attributs de configuration.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($attrs, ListTable $pattern)
    {
        $this->pattern = $pattern;

        if (is_object($attrs)) :
            $attrs = get_object_vars($attrs);
        endif;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimary($default = null)
    {
        return $this->get($this->getPrimaryKey(), $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        if (($primary_key = $this->pattern->param('item_primary_key')) && $this->has($primary_key)) :
            return $primary_key;
        elseif ($db = $this->pattern->db()) :
            return $db->getPrimary();
        else :
            return current($this->keys());
        endif;
    }
}