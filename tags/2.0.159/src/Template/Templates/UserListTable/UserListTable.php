<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable;

use tiFy\Template\Templates\ListTable\Contracts\Item as BaseItem;
use tiFy\Template\Templates\ListTable\ListTable as BaseListTable;
use tiFy\Template\Templates\UserListTable\Contracts\{UserListTable as UserListTableContract};

class UserListTable extends BaseListTable implements UserListTableContract
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        UserListTableServiceProvider::class,
    ];

    /**
     * @inheritDoc
     *
     * @return Item
     */
    public function item(): ?BaseItem
    {
        return parent::item();
    }
}