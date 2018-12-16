<?php

namespace tiFy\Wp\View\Pattern\PostListTable;

use tiFy\View\Pattern\ListTable\ListTable as BaseListTable;
use tiFy\Wp\View\Pattern\PostListTable\Contracts\PostListTable as PostListTableContract;

class PostListTable extends BaseListTable implements PostListTableContract
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        PostListTableServiceProvider::class,
    ];
}