<?php declare(strict_types=1);

namespace tiFy\Database;

use tiFy\Container\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'database',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('database', function () {
            $manager = new Database();

            $manager->addConnection([
                'driver'    => env('DB_CONNECTION'),
                'host'      => env('DB_HOST') . (env('DB_PORT') ? ':' . env('DB_PORT') : ''),
                'database'  => env('DB_DATABASE'),
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
                'charset'   => env('DB_CHARSET') ?: 'utf8mb4',
                'collation' => env('DB_COLLATE') ?: 'utf8mb4_unicode_ci',
                'prefix'    => env('DB_PREFIX') ?: '',
            ]);

            $manager->setAsGlobal();

            $manager->bootEloquent();

            return $manager;
        });
    }
}