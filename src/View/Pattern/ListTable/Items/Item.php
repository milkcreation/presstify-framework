<?php

namespace tiFy\View\Pattern\ListTable\Items;

use Illuminate\Support\Arr;
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
    public function getPrimary()
    {
         if (($db = $this->pattern->db()) && ($primary = $db->getPrimary()) && $this->has($primary)) :
            return $this->get($primary);
         else :
            return Arr::first($this->attributes);
         endif;
    }
}