<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

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
     * Objet de délégation d'appel des méthodes de la classe.
     * @var QueryPostContract|object|null
     */
    protected $delegate;

    /**
     * @inheritDoc
     */
    public function parse(): BaseItemContract
    {
        parent::parse();

        if (is_null($this->delegate)) {
            $this->setDelegate(QueryPost::createFromId($this->getKeyValue()));
        }

        return $this;
    }
}