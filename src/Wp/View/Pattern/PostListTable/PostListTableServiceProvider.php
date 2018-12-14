<?php

namespace tiFy\Wp\View\Pattern\PostListTable;

use tiFy\View\Pattern\ListTable\ListTableServiceProvider;
use tiFy\PostType\Db\DbPostsController;
use tiFy\Wp\View\Pattern\PostListTable\Columns\ColumnsItemPostTitle;
use tiFy\Wp\View\Pattern\PostListTable\Columns\ColumnsItemPostType;
use tiFy\Wp\View\Pattern\PostListTable\Contracts\PostListTable;
use tiFy\Wp\View\Pattern\PostListTable\Labels\Labels;
use tiFy\Wp\View\Pattern\PostListTable\Params\Params;
use tiFy\Wp\View\Pattern\PostListTable\Request\Request;
use tiFy\Wp\View\Pattern\PostListTable\ViewFilters\ViewFiltersItemAll;
use tiFy\Wp\View\Pattern\PostListTable\ViewFilters\ViewFiltersItemPublish;
use tiFy\Wp\View\Pattern\PostListTable\ViewFilters\ViewFiltersItemTrash;

class PostListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        array_push(
            $this->provides,
            'columns.item.post_title',
            'view-filters.item.all',
            'view-filters.item.publish',
            'view-filters.item.trash'
        );

        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function registerColumns()
    {
        parent::registerColumns();

        $this->getContainer()->add($this->getFullAlias('columns.item.post_title'), ColumnsItemPostTitle::class);

        $this->getContainer()->add($this->getFullAlias('columns.item.post_type'), ColumnsItemPostType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function registerDb()
    {
        $this->getContainer()->share($this->getFullAlias('db'), function(PostListTable $pattern) {
            return new DbPostsController($pattern->name());
        })->withArgument($this->getContainer());
    }

    /**
     * {@inheritdoc}
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function (PostListTable $pattern) {
            return new Labels($pattern->name(), $this->config('labels', []));
        })->withArgument($this->getContainer());
    }

    /**
     * {@inheritdoc}
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function (PostListTable $pattern) {
            return new Params($this->config('params', []), $pattern);
        })->withArgument($this->getContainer());
    }

    /**
     * {@inheritdoc}
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function (PostListTable $pattern) {
            return (Request::capture())->setPattern($pattern);
        })->withArgument($this->getContainer());
    }

    /**
     * {@inheritdoc}
     */
    public function registerViewFilters()
    {
        parent::registerViewFilters();

        $this->getContainer()->add($this->getFullAlias('view-filters.item.all'), ViewFiltersItemAll::class);

        $this->getContainer()->add($this->getFullAlias('view-filters.item.publish'), ViewFiltersItemPublish::class);

        $this->getContainer()->add($this->getFullAlias('view-filters.item.trash'), ViewFiltersItemTrash::class);
    }
}