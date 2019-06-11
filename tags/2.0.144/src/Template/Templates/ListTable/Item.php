<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Contracts\Template\FactoryDb;
use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{Item as ItemContract, ListTable};

class Item extends ParamsBag implements ItemContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Instance de l'objet associé.
     * @var object
     */
    protected $object;

    /**
     * @inheritDoc
     */
    public function getKeyName(): string
    {
        if (($primary_key = $this->factory->param('item_primary_key')) && $this->has($primary_key)) {
            return (string)$primary_key;
        } elseif ($db = $this->factory->db()) {
            return $db->getKeyName();
        } else {
            return current($this->keys());
        }
    }

    /**
     * @inheritDoc
     */
    public function getKeyValue($default = null)
    {
        return $this->get($this->getKeyName(), $default);
    }

    /**
     * @inheritDoc
     */
    public function model(): ?FactoryDb
    {
        return $this->object instanceof FactoryDb ? $this->object : null;
    }

    /**
     * @inheritDoc
     */
    public function setObject(object $object): ItemContract
    {
        $this->object = $object;

        return $this;
    }
}