<?php

namespace tiFy\Template\Templates\ListTable\Items;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\Item as ItemContract;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Item extends ParamsBag implements ItemContract
{
    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param mixed $attrs Liste des attributs de configuration.
     * @param ListTable $template Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($attrs, ListTable $template)
    {
        $this->template = $template;

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
        if (($primary_key = $this->template->param('item_primary_key')) && $this->has($primary_key)) :
            return $primary_key;
        elseif ($db = $this->template->db()) :
            return $db->getPrimary();
        else :
            return current($this->keys());
        endif;
    }
}