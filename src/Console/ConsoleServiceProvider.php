<?php

declare(strict_types=1);

namespace tiFy\Console;

use Symfony\Component\Console\Input\InputOption;
use tiFy\Console\Commands\UpdateCore20345;
use Pollen\Container\BootableServiceProvider;
use Pollen\Kernel\ApplicationInterface;

class ConsoleServiceProvider extends BootableServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = ['console'];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        register_shutdown_function(function () {
            /** @var ApplicationInterface $app */
            $app = $this->getContainer()->get('app');

            if($app->runningInConsole()) {
                $this->getContainer()->get('console')->run();
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('console', function() {
            $app = new Console('presstiFy PHP CLI Console', '1.0.0');

            foreach (config('console.commands', []) as $k => $command) {
                if (is_numeric($k) && class_exists($command)) {
                    $command = $app->add(new $command());
                } elseif (class_exists($command)) {
                    $command = $app->add(new $command($k));
                }

                if (!$command->getDefinition()->hasOption('url')) {
                    $command->addOption(
                        'url',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'site url'
                    );
                }
            }

            $app->add((new UpdateCore20345())->setName('update-core:2.0.345'));

            return $app;
        });
    }
}