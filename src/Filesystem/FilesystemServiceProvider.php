<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\{
    AdapterInterface,
    Cached\CachedAdapter,
    Cached\CacheInterface,
    Cached\Storage\Memory as MemoryStore};
use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Filesystem\{
    Filesystem as FilesystemContract,
    LocalFilesystem as LocalFilesystemContract,
    LocalAdapter as LocalAdapterContract};

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
    public function register():void
    {
        $this->getContainer()->share('storage', function () {
            return new StorageManager($this->getContainer());
        });

        $this->registerAdapter();
        $this->registerFilesystem();
    }

    /**
     * @inheritDoc
     */
    public function registerAdapter():void
    {
        $this->getContainer()->add(LocalAdapterContract::class, function (string $root, array $config = []) {
            $permissions = $config['permissions'] ?? [];
            $links = ($config['links'] ?? null) === 'skip'
                ? LocalAdapter::SKIP_LINKS
                : LocalAdapter::DISALLOW_LINKS;

            $adapter = new LocalAdapter($root, LOCK_EX, $links, $permissions);

            if ($cache = $config['cache'] ?? true) {
                $adapter = $cache instanceof CacheInterface
                    ? new CachedAdapter($adapter, $cache)
                    : new CachedAdapter($adapter, new MemoryStore());
            }

            return $adapter;
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFilesystem():void
    {
        $this->getContainer()->add(FilesystemContract::class, function (AdapterInterface $adapter) {
            return new Filesystem($adapter);
        });

        $this->getContainer()->add(LocalFilesystemContract::class, function (string $root, array $config = []) {
            $adapter = $this->getContainer()->get(LocalAdapterContract::class, [$root, $config]);

            return new LocalFilesystem($adapter, [
                'disable_asserts' => true,
                'case_sensitive' => true
            ]);
        });
    }
}