<?php

namespace tiFy\Layout\Share\WpUserListTable;

use tiFy\Layout\Share\WpUserListTable\Column\ColumnItemRoleController;
use tiFy\Layout\Share\WpUserListTable\Column\ColumnItemUserLoginController;
use tiFy\Layout\Share\WpUserListTable\Column\ColumnItemUserRegisteredController;
use tiFy\Layout\Share\WpUserListTable\Item\ItemCollectionController;
use tiFy\Layout\Share\WpUserListTable\Item\ItemController;
use tiFy\Layout\Share\WpUserListTable\Labels\LabelsController;
use tiFy\Layout\Share\WpUserListTable\Params\ParamsController;
use tiFy\Layout\Share\WpUserListTable\Request\RequestController;
use tiFy\Layout\Share\ListTable\ListTableServiceProvider as ShareListTableServiceProvider;
use tiFy\Layout\Base\DbUsersController;

class WpUserListTableServiceProvider extends ShareListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->getContainer()->bind('columns.item.role', ColumnItemRoleController::class);
        $this->getContainer()->bind('columns.item.user_login', ColumnItemUserLoginController::class);
        $this->getContainer()->bind('columns.item.user_registered', ColumnItemUserRegisteredController::class);

        $this->getContainer()->singleton('db', DbUsersController::class);

        $this->getContainer()->singleton('items', ItemCollectionController::class);
        $this->getContainer()->bind('item', ItemController::class);

        $this->getContainer()->singleton('labels', LabelsController::class);

        $this->getContainer()->singleton('params', ParamsController::class);

        $this->getContainer()->singleton('request', RequestController::class);
    }
}