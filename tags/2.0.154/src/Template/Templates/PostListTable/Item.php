<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Contracts\Template\FactoryDb;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{Item as BaseItemContract, ListTable};
use tiFy\Template\Templates\PostListTable\Contracts\Item as ItemContract;
use tiFy\Wordpress\Query\QueryPost;

class Item extends QueryPost implements ItemContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Indice de l'élément.
     * @var int
     */
    protected $index;

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
    public function getIndex(): int
    {
        return $this->index;
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
    public function setIndex(int $index): BaseItemContract
    {
        if (is_null($this->index)) {
            $this->index = $index;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setObject(object $object): BaseItemContract
    {
        $this->object = $object;

        return $this;
    }
}