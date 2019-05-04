<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\AdapterInterface;
use tiFy\Container\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'filesystem',
        'storage'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->add('filesystem', function (AdapterInterface $adapter) {
            return new Filesystem($adapter);
        });

        $this->getContainer()->share('storage', function () {
            return new StorageManager();
        });
    }
}