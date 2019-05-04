<?php

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\ListTableServiceProvider;
use tiFy\PostType\Db\DbPostsController;
use tiFy\Template\Templates\PostListTable\Columns\ColumnsItemPostTitle;
use tiFy\Template\Templates\PostListTable\Columns\ColumnsItemPostType;
use tiFy\Template\Templates\PostListTable\Contracts\PostListTable;
use tiFy\Template\Templates\PostListTable\Labels\Labels;
use tiFy\Template\Templates\PostListTable\Params\Params;
use tiFy\Template\Templates\PostListTable\Request\Request;
use tiFy\Template\Templates\PostListTable\ViewFilters\ViewFiltersItemAll;
use tiFy\Template\Templates\PostListTable\ViewFilters\ViewFiltersItemPublish;
use tiFy\Template\Templates\PostListTable\ViewFilters\ViewFiltersItemTrash;

class PostListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var PostListTable
     */
    protected $factory;

    /**
     * @inheritdoc
     */
    public function registerColumns()
    {
        parent::registerColumns();

        $this->getContainer()->add(
            $this->getFullAlias('columns.item.post_title'),
            function ($name, $attrs, ListTable $factory) {
                return new ColumnsItemPostTitle($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFullAlias('columns.item.post_type'),
            function ($name, $attrs, ListTable $factory) {
                return new ColumnsItemPostType($name, $attrs, $factory);
            });
    }

    /**
     * @inheritdoc
     */
    public function registerDb()
    {
        $this->getContainer()->share($this->getFullAlias('db'), function() {
            return new DbPostsController($this->factory->name());
        });
    }

    /**
     * @inheritdoc
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function () {
            return new Labels($this->factory);
        });
    }

    /**
     * @inheritdoc
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function () {
            return new Params($this->factory);
        });
    }

    /**
     * @inheritdoc
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function () {
            return (Request::capture())->setTemplateFactory($this->factory);
        });
    }

    /**
     * @inheritdoc
     */
    public function registerViewFilters()
    {
        parent::registerViewFilters();

        $this->getContainer()->add(
            $this->getFullAlias('view-filters.item.all'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItemAll($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFullAlias('view-filters.item.publish'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItemPublish($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFullAlias('view-filters.item.trash'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItemTrash($name, $attrs, $factory);
            });
    }
}