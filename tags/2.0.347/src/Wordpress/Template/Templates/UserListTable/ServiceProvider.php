<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template\Templates\UserListTable;

use Illuminate\Database\Eloquent\Model;
use tiFy\Contracts\Template\FactoryDb;
use tiFy\Template\Factory\Db;
use tiFy\Template\Templates\ListTable\{
    Contracts\Builder as BaseBuilderContract,
    ServiceProvider as BaseServiceProvider
};
use tiFy\Wordpress\Database\Model\User as UserModel;
use tiFy\Wordpress\Template\Templates\UserListTable\Contracts\{Item, DbBuilder};


class ServiceProvider extends BaseServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function registerFactoryBuilder(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('builder'), function () {
            $ctrl = $this->factory->provider('builder');

            if ($this->factory->db()) {
                $ctrl = $ctrl instanceof DbBuilder
                    ? $ctrl
                    : $this->getContainer()->get(DbBuilder::class);
            } else {
                $ctrl = $ctrl instanceof BaseBuilderContract
                    ? $ctrl
                    : $this->getContainer()->get(BaseBuilderContract::class);
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
            $db = $this->factory->provider('db', '');

            if (!is_null($db)) {
                if ($db instanceof Model) {
                    $db = (new Db())->setDelegate($db);
                } elseif (!$db instanceof FactoryDb) {
                    $db = (new Db())->setDelegate(new UserModel());
                }

                return  $db->setTemplateFactory($this->factory);
            } else {
                return null;
            }
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
            $ctrl = $this->factory->provider('item');

            $ctrl = $ctrl instanceof Item
                ? clone $ctrl
                : $this->getContainer()->get(Item::class);

            return $ctrl->setTemplateFactory($this->factory);
        });
    }
}