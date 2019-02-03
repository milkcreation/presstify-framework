<?php

namespace tiFy\Cron;

use tiFy\App\Container\AppServiceProvider;

class CronServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'cron',
        'cron.job'
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('after_setup_tify', function() {
            $this->getContainer()->get('cron');
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('cron', function () {
            return new CronManager();
        });

        $this->getContainer()->add('cron.job', function ($name, $attrs) {
            return new CronJob($name, $attrs);
        });
    }
}