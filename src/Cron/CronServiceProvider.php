<?php

declare(strict_types=1);

namespace tiFy\Cron;

use Pollen\Container\BaseServiceProvider;

class CronServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'cron',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('cron', function () {
            return (new CronManager($this->getContainer()->get('app')))->set(config('cron', []));
        });
    }
}