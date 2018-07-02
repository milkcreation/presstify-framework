<?php

namespace tiFy\Components\Layout\PostListTable;

use tiFy\Components\Layout\ListTable\ListTable;

class PostListTable extends ListTable
{
    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = PostListTableServiceProvider::class;
}