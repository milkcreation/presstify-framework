<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Template\Templates\ListTable\ListTableServiceProvider;
use tiFy\Template\Templates\PostListTable\Contracts\{
    Db,
    Item,
    Params,
    Builder,
    PostListTable};

class PostListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var PostListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function registerFactoryBuilder(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('builder'), function () {
            $ctrl = $this->factory->get('providers.builder');
            $ctrl = $ctrl instanceof Builder
                ? clone $ctrl
                : $this->getContainer()->get(Builder::class);

            $attrs = $this->factory->param('query_args', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryColumns(): void
    {
        parent::registerFactoryColumns();

        $this->getContainer()->add(
            $this->getFactoryAlias('column.post_title'),
            function (string $name, array $attrs = []) {
                return (new ColumnPostTitle())
                    ->setTemplateFactory($this->factory)
                    ->setName($name)
                    ->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('column.post_type'),
            function (string $name, array $attrs = []) {
                return (new ColumnPostType())
                    ->setTemplateFactory($this->factory)
                    ->setName($name)
                    ->set($attrs)->parse();
            });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryDb(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('db'), function () {
            $ctrl = $this->factory->get('providers.db');
            $ctrl = $ctrl instanceof Db
                ? $ctrl
                : $this->getContainer()->get(Db::class);

            return $ctrl->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur d'un élément.
     *
     * @return void
     */
    public function registerFactoryItem(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('item'), function () {
            $ctrl = $this->factory->get('providers.item');

            $ctrl = $ctrl instanceof Item
                ? clone $ctrl
                : $this->getContainer()->get(Item::class);

            return $ctrl->setTemplateFactory($this->factory);
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryLabels(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('labels'), function () {
            return new Labels($this->factory);
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('params'), function () {
            $ctrl = $this->factory->get('providers.params');
            $ctrl = $ctrl instanceof Params
                ? $ctrl
                : $this->getContainer()->get(Params::class);

            $attrs = $this->factory->get('params', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : [])->parse();
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryViewFilters(): void
    {
        parent::registerFactoryViewFilters();

        $this->getContainer()->add($this->getFactoryAlias('view-filter.all'),
            function (string $name, array $attrs = []) {
                return (new ViewFilterAll())
                    ->setTemplateFactory($this->factory)
                    ->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('view-filter.publish'),
            function (string $name, array $attrs = []) {
                return (new ViewFilterPublish())
                    ->setTemplateFactory($this->factory)
                    ->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('view-filter.trash'),
            function (string $name, array $attrs = []) {
                return (new ViewFilterTrash())
                    ->setTemplateFactory($this->factory)
                    ->setName($name)->set($attrs)->parse();
            });
    }
}