<?php

namespace tiFy\Db;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Db\Db;
use tiFy\Db\DbItemHandleController;
use tiFy\Db\DbItemMakeController;
use tiFy\Db\DbItemMetaController;
use tiFy\Db\DbItemMetaQueryController;
use tiFy\Db\DbItemParserController;
use tiFy\Db\DbItemQueryController;
use tiFy\Db\DbItemSelectController;

class DbServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [
        Db::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->resolve(Db::class);
        $this->app->bind('db.item.handle', DbItemHandleController::class);
        $this->app->bind('db.item.make', DbItemMakeController::class);
        $this->app->bind('db.item.meta', DbItemMetaController::class);
        $this->app->bind('db.item.meta_query', DbItemMetaQueryController::class);
        $this->app->bind('db.item.parser', DbItemParserController::class);
        $this->app->bind('db.item.query', DbItemQueryController::class);
        $this->app->bind('db.item.select', DbItemSelectController::class);
    }
}