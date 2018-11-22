<?php

namespace tiFy\Layout\Share\WpPostListTable;

use tiFy\Layout\Share\ListTable\ListTable as ShareListTable;

class WpPostListTable extends ShareListTable
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        WpPostListTableServiceProvider::class
    ];
}