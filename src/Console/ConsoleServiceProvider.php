<?php

namespace tiFy\Console;

use tiFy\App\Container\AppServiceProvider;

class ConsoleServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'console.controller.application',
        'console.controller.kernel'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('console.controller.application', function() {
            return (new ControllerApplication($this->getContainer()->get('console.controller.kernel')))->setCommands();
        });

        $this->getContainer()->share('console.controller.kernel', function() {
            return new ControllerKernel(getenv('APP_ENV') ?? '', getenv('APP_DEBUG') ?? false);
        });
    }
}