<?php

namespace tiFy\Layout\Share\WpPostListTable;

use tiFy\Layout\Share\ListTable\ListTableServiceProvider;
use tiFy\Layout\Share\WpPostListTable\Column\ColumnItemPostTitleController;
use tiFy\Layout\Share\WpPostListTable\Params\ParamsController;
use tiFy\Layout\Share\WpPostListTable\Request\RequestController;
use tiFy\Layout\Share\WpPostListTable\ViewFilter\ViewFilterItemAllController;
use tiFy\Layout\Share\WpPostListTable\ViewFilter\ViewFilterItemPublishController;
use tiFy\Layout\Share\WpPostListTable\ViewFilter\ViewFilterItemTrashController;
use tiFy\Layout\Base\DbPostsController;
use tiFy\Layout\Base\LabelsPostsController;

class WpPostListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->getContainer()->bind('layout.columns.item.post_title', ColumnItemPostTitleController::class);

        $this->getContainer()->singleton('layout.db', DbPostsController::class);

        $this->getContainer()->singleton('layout.labels', LabelsPostsController::class);

        $this->getContainer()->singleton('layout.params', ParamsController::class);

        $this->getContainer()->singleton('layout.request', RequestController::class);

        $this->getContainer()->bind('layout.view_filters.item.all', ViewFilterItemAllController::class);
        $this->getContainer()->bind('layout.view_filters.item.publish', ViewFilterItemPublishController::class);
        $this->getContainer()->bind('layout.view_filters.item.trash', ViewFilterItemTrashController::class);
    }
}