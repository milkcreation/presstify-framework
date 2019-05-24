<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Filesystem\Filesystem as FilesystemContract;
use tiFy\Contracts\Filesystem\StorageManager as StorageManagerContract;

class StorageManager extends MountManager implements StorageManagerContract
{
    /**
     * Instance du conteneur d'injection de dépendance.
     * @var Container
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container $container Instance du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function createLocal(string $root, array $config = []): FilesystemContract
    {
        $permissions = $config['permissions'] ?? [];
        $links = ($config['links'] ?? null) === 'skip'
            ? Local::SKIP_LINKS
            : Local::DISALLOW_LINKS;

        $params = [
            'disable_asserts' => true,
            'case_sensitive' => true
        ];

        $adapter = new Local($root, LOCK_EX, $links, $permissions);

        if ($cache = $config['cache'] ?? true) {
            $adapter = $cache instanceof CacheInterface
                ? new CachedAdapter($adapter, $cache)
                : new CachedAdapter($adapter, new MemoryStore());
        }

        return $this->getContainer()->has(FilesystemContract::class)
            ? $this->getContainer()->get(FilesystemContract::class, [$adapter, $params])
            : new Filesystem($adapter, $params);
    }

    /**
     * @inheritDoc
     */
    public function disk(string $name): FilesystemContract
    {
        return $this->getFilesystem($name);
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container instanceof Container ? $this->container : null;
    }

    /**
     * @inheritDoc
     */
    public function getFilesystem($prefix)
    {
        return parent::getFilesystem($prefix);
    }

    /**
     * @inheritDoc
     */
    public function mountFilesystem($name, FilesystemInterface $filesystem)
    {
        if ($filesystem instanceof FilesystemContract) {
            return parent::mountFilesystem($name, $filesystem);
        }
        throw new InvalidArgumentException(
            sprintf(
                __('Impossible de monter le disque %s. Le gestionnaire de fichiers doit une instance de %s.', 'tify'),
                $name,
                FilesystemContract::class
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, $attrs): StorageManagerContract
    {
        $filesystem = !$attrs instanceof Filesystem ? $this->createLocal($attrs['root']?? '', $attrs) : $attrs;

        return $this->set($name, $filesystem);
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, FilesystemContract $filesystem): StorageManagerContract
    {
        return $this->mountFilesystem($name, $filesystem);
    }
}