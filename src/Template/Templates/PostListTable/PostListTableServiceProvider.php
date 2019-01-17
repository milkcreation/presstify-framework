<?php

namespace tiFy\Template\Templates\PostListTable;

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
        $this->getContainer()->share($this->getFullAlias('db'), function(PostListTable $template) {
            return new DbPostsController($template->name());
        })->withArgument($this->template);
    }

    /**
     * {@inheritdoc}
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function (PostListTable $template) {
            return new Labels($template->name(), $template->config('labels', []), $template);
        })->withArgument($this->template);
    }

    /**
     * {@inheritdoc}
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function (PostListTable $template) {
            return new Params($template->config('params', []), $template);
        })->withArgument($this->template);
    }

    /**
     * {@inheritdoc}
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function (PostListTable $template) {
            return (Request::capture())->setTemplate($template);
        })->withArgument($this->template);
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