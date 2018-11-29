<?php

namespace tiFy\Layout\Share\ListTable\Item;

use Illuminate\Support\Arr;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Layout\Share\ListTable\Contracts\ItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class ItemController extends ParamsBag implements ItemInterface
{
    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct($attrs = [], ListTableInterface $layout)
    {
        $this->layout = $layout;

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
         if (($db = $this->layout->db()) && ($primary = $db->getPrimary()) && $this->has($primary)) :
            return $this->get($primary);
         else :
            return Arr::first($this->attributes);
         endif;
    }
}