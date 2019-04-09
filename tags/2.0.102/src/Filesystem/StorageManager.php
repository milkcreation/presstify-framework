<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\Plugin\ForcedCopy;
use tiFy\Contracts\Filesystem\Filesystem;
use tiFy\Contracts\Filesystem\StorageManager as StorageManagerContract;

class StorageManager extends MountManager implements StorageManagerContract
{
    /**
     * @inheritdoc
     */
    public function createLocal(array $config)
    {
        $permissions = $config['permissions'] ?? [];
        $links = ($config['links'] ?? null) === 'skip'
            ? LocalAdapter::SKIP_LINKS
            : LocalAdapter::DISALLOW_LINKS;

        return  app()->get('filesystem', [new LocalAdapter($config['root'], LOCK_EX, $links, $permissions)]);
    }

    /**
     * @inheritdoc
     */
    public function disk(string $name)
    {
        return $this->getFilesystem($name);
    }

    /**
     * @inheritdoc
     */
    public function register(string $name, $attrs)
    {
        if (!$attrs instanceof Filesystem) :
            $filesystem = $this->createLocal($attrs);
        else :
            $filesystem = $attrs;
        endif;

        return $this->set($name, $filesystem);
    }

    /**
     * @inheritdoc
     */
    public function set(string $name, Filesystem $filesystem)
    {
        return $this->mountFilesystem($name, $filesystem);
    }
}