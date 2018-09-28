<?php

namespace tiFy\Layout\Share\AjaxListTable;

use tiFy\Layout\Share\AjaxListTable\Params\ParamsController;
use tiFy\Layout\Share\AjaxListTable\Request\RequestController;
use tiFy\Layout\Share\WpPostListTable\WpPostListTableServiceProvider;

class AjaxListTableServiceProvider extends WpPostListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->getContainer()->singleton('params', ParamsController::class);

        $this->getContainer()->singleton('request', RequestController::class);
    }
}