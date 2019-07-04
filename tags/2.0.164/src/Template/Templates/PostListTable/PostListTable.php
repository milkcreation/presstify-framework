<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Contracts\Template\{FactoryDb, TemplateFactory};
use tiFy\Template\Templates\ListTable\ListTable as BaseListTable;
use tiFy\Template\Templates\ListTable\Contracts\{Item as BaseItem};
use tiFy\Template\Templates\PostListTable\Contracts\{Db, Item, PostListTable as PostListTableContract};

class PostListTable extends BaseListTable implements PostListTableContract
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        PostListTableServiceProvider::class,
    ];

    /**
     * {@inheritDoc}
     *
     * @return Db
     */
    public function db(): FactoryDb
    {
        return parent::db();
    }

    /**
     * @inheritDoc
     *
     * @return Item
     */
    public function item(): ?BaseItem
    {
        return parent::item();
    }

    /**
     * {@inheritDoc}
     *
     * @return PostListTableContract
     */
    public function prepare(): TemplateFactory
    {
        return parent::prepare();
    }
}