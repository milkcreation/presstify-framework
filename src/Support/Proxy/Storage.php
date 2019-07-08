<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Filesystem\{Filesystem, LocalAdapter, LocalFilesystem, StorageManager};

/**
 * @method static Filesystem|null disk(string $name)
 * @method static LocalFilesystem local(string|LocalAdapter $root, array $config = [])
 * @method static LocalAdapter localAdapter(string $root, array $config = [])
 * @method static StorageManager set(string $name, Filesystem $filesystem)
 */
class Storage extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'storage';
    }
}