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

        $this->getContainer()->bind('layout.columns.item.role', ColumnItemRoleController::class);
        $this->getContainer()->bind('layout.columns.item.user_login', ColumnItemUserLoginController::class);
        $this->getContainer()->bind('layout.columns.item.user_registered', ColumnItemUserRegisteredController::class);

        $this->getContainer()->singleton('layout.db', DbUsersController::class);

        $this->getContainer()->singleton('layout.items', ItemCollectionController::class);
        $this->getContainer()->bind('layout.item', ItemController::class);

        $this->getContainer()->singleton('layout.labels', LabelsController::class);

        $this->getContainer()->singleton('layout.params', ParamsController::class);

        $this->getContainer()->singleton('layout.request', RequestController::class);
    }
}