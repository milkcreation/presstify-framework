<?php declare(strict_types=1);

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
    public function registerColumns(): void
    {
        parent::registerFactoryColumns();

        $this->getContainer()->add(
            $this->getFactoryAlias('columns.item.post_title'),
            function ($name, $attrs, ListTable $factory) {
                return new ColumnsItemPostTitle($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('columns.item.post_type'),
            function ($name, $attrs, ListTable $factory) {
                return new ColumnsItemPostType($name, $attrs, $factory);
            });
    }

    /**
     * @inheritdoc
     */
    public function registerFactoryDb(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('db'), function() {
            return new DbPostsController($this->factory->name());
        });
    }

    /**
     * @inheritdoc
     */
    public function registerFactoryLabels(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('labels'), function () {
            return new Labels($this->factory);
        });
    }

    /**
     * @inheritdoc
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('params'), function () {
            return new Params($this->factory);
        });
    }

    /**
     * @inheritdoc
     */
    public function registerFactoryRequest(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('request'), function () {
            return (Request::capture())->setTemplateFactory($this->factory);
        });
    }

    /**
     * @inheritdoc
     */
    public function registerFactoryViewFilters(): void
    {
        parent::registerFactoryViewFilters();

        $this->getContainer()->add(
            $this->getFactoryAlias('view-filters.item.all'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItemAll($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('view-filters.item.publish'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItemPublish($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('view-filters.item.trash'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItemTrash($name, $attrs, $factory);
            });
    }
}