<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use BadMethodCallException;
use Exception;
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
     * Indice de l'élément.
     * @var int
     */
    protected $index;

    /**
     * Objet de délégation d'appel des méthodes de la classe.
     * @var object|null
     */
    protected $delegate;

    /**
     * @inheritDoc
     */
    public function __call($name, $args)
    {
        try {
            return $this->delegate->$name(...$args);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }

    /**
     * @inheritDoc
     */
    public function getKeyName(): string
    {
        if (($primary_key = $this->factory->param('primary_key')) && $this->has($primary_key)) {
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
    public function parse(): ItemContract
    {
        parent::parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDelegate(object $delegate): ItemContract
    {
        $this->delegate = $delegate;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(int $index): ItemContract
    {
        if (is_null($this->index)) {
            $this->index = $index;
        }

        return $this;
    }
}