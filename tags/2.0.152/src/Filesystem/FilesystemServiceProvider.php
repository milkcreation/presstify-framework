<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\AdapterInterface;
use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Filesystem\Filesystem as FilesystemContract;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        FilesystemContract::class,
        'storage'
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->add(FilesystemContract::class, function (AdapterInterface $adapter) {
            return new Filesystem($adapter);
        });

        $this->getContainer()->share('storage', function () {
            return new StorageManager($this->getContainer());
        });
    }
}