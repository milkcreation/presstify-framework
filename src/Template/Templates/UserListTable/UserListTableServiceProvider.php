<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable;

use tiFy\Template\Templates\UserListTable\Contracts\{Db, Item, DbBuilder};
use tiFy\Template\Templates\ListTable\Contracts\Builder;
use tiFy\Template\Templates\ListTable\ListTableServiceProvider;

class UserListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var UserListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function registerFactoryBuilder(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('builder'), function () {
            $ctrl = $this->factory->get('providers.builder');

            if ($this->factory->db()) {
                $ctrl = $ctrl instanceof DbBuilder
                    ? clone $ctrl
                    : $this->getContainer()->get(DbBuilder::class);
            } else {
                $ctrl = $ctrl instanceof Builder
                    ? clone $ctrl
                    : $this->getContainer()->get(Builder::class);
            }


            $attrs = $this->factory->param('query_args', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
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
}