<?php

namespace tiFy\Cron;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Cron\Cron;
use tiFy\Cron\CronJobBaseController;

class CronServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('cron', function () { return new Cron();})->build();
        $this->app->bind('cron.job', CronJobBaseController::class);
    }
}