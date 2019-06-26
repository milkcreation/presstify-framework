<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use BadMethodCallException;
use Exception;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Item as BaseItem;
use tiFy\Template\Templates\ListTable\Contracts\{Item as BaseItemContract};
use tiFy\Template\Templates\PostListTable\Contracts\Item as ItemContract;
use tiFy\Wordpress\Contracts\QueryPost as QueryPostContract;
use tiFy\Wordpress\Query\QueryPost;

/**
 * @mixin QueryPost
 */
class Item extends BaseItem implements ItemContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var PostListTable
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
     * Objet de délégation associé.
     * @var QueryPostContract
     */
    protected $delegateObject;

    /**
     * @inheritDoc
     */
    public function __call($name, $args)
    {
        try {
            return $this->delegateObject->$name(...$args);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }

    /**
     * @inheritDoc
     */
    public function parse(): BaseItemContract
    {
        parent::parse();

        $this->delegateObject = QueryPost::createFromId($this->getKeyValue());

        return $this;
    }
}