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
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('db', function() { return new Db();})->build();
        $this->app->bind('db.item.handle', DbItemHandleController::class);
        $this->app->bind('db.item.make', DbItemMakeController::class);
        $this->app->bind('db.item.meta', DbItemMetaController::class);
        $this->app->bind('db.item.meta_query', DbItemMetaQueryController::class);
        $this->app->bind('db.item.parser', DbItemParserController::class);
        $this->app->bind('db.item.query', DbItemQueryController::class);
        $this->app->bind('db.item.select', DbItemSelectController::class);
    }
}