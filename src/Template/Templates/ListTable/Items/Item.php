<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Items;

use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\Item as ItemContract;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Item extends ParamsBag implements ItemContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param mixed $attrs Liste des attributs de configuration.
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($attrs, ListTable $factory)
    {
        $this->factory = $factory;

        if (is_object($attrs)) {
            $attrs = get_object_vars($attrs);
        }

        $this->set($attrs)->parse();
    }

    /**
     * @inheritdoc
     */
    public function getPrimary($default = null)
    {
        return $this->get($this->getPrimaryKey(), $default);
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKey()
    {
        if (($primary_key = $this->factory->param('item_primary_key')) && $this->has($primary_key)) {
            return $primary_key;
        } elseif ($db = $this->factory->db()) {
            return $db->getPrimary();
        } else {
            return current($this->keys());
        }
    }
}