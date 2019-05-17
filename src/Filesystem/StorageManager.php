<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\MountManager;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Filesystem\Filesystem;
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
     * @param Container $container
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        parent::__construct([]);
    }

    /**
     * @inheritDoc
     */
    public function createLocal(array $config)
    {
        $permissions = $config['permissions'] ?? [];
        $links = ($config['links'] ?? null) === 'skip'
            ? LocalAdapter::SKIP_LINKS
            : LocalAdapter::DISALLOW_LINKS;

        return  $this->container->get(Filesystem::class, [new LocalAdapter($config['root'], LOCK_EX, $links, $permissions)]);
    }

    /**
     * @inheritDoc
     */
    public function disk(string $name)
    {
        return $this->getFilesystem($name);
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, $attrs)
    {
        if (!$attrs instanceof Filesystem) {
            $filesystem = $this->createLocal($attrs);
        } else {
            $filesystem = $attrs;
        }

        return $this->set($name, $filesystem);
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, Filesystem $filesystem)
    {
        return $this->mountFilesystem($name, $filesystem);
    }
}