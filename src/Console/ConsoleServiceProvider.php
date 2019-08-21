<?php declare(strict_types=1);

namespace tiFy\Console;

use tiFy\Container\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
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
     * @inheritDoc
     */
    public function boot()
    {
        add_action('shutdown', function () {
            global $argv;

            if(isset($argv[0]) && preg_match('/vendor\/bin\/bee$/', $argv[0])) {
                $this->getContainer()->get('console.controller.application')->run();
            }
        }, 999999);
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->share('console.controller.application', function() {
            $app = new ControllerApplication($this->getContainer()->get('console.controller.kernel'));
            $app->setName('bee');
            $app->setVersion('1.0.0');
            return $app->setCommands();
        });

        $this->getContainer()->share('console.controller.kernel', function() {
            return new ControllerKernel(getenv('APP_ENV') ?? '', (bool)(getenv('APP_DEBUG') ?? false));
        });
    }
}