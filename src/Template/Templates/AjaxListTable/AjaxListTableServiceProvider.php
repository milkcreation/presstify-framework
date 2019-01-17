<?php

namespace tiFy\Layout\Share\AjaxListTable;

use tiFy\Layout\Share\AjaxListTable\Params\ParamsController;
use tiFy\Layout\Share\AjaxListTable\Request\RequestController;
use tiFy\Layout\Share\ListTable\ListTableServiceProvider;

class AjaxListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->getContainer()->singleton('layout.db', false);

        $this->getContainer()->singleton('layout.params', ParamsController::class);

        $this->getContainer()->singleton('layout.request', RequestController::class);
    }
}